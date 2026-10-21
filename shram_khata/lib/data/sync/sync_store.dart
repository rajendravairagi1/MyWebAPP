import 'package:drift/drift.dart';

import '../database.dart';

/// One row to send to / received from the server.
class SyncChange {
  SyncChange({required this.table, required this.id, this.data = const {}, this.deleted = false});
  final String table;
  final String id;
  final Map<String, Object?> data;
  final bool deleted;

  Map<String, Object?> toJson() => {'t': table, 'id': id, 'd': data, 'del': deleted};
}

/// A batch of local changes plus the outbox position to clear once sent.
class PendingBatch {
  PendingBatch(this.changes, this.upToId);
  final List<SyncChange> changes;
  final int upToId;
  bool get isEmpty => changes.isEmpty;
}

/// Local half of cloud sync: remembers what changed (via database triggers),
/// turns rows into JSON and applies rows that arrive from the server.
class SyncStore {
  SyncStore(this.db);
  final AppDatabase db;

  /// Names of the tables that sync, with their key column.
  List<({String table, String pk})> get tables => [
        for (final t in db.allTables)
          (
            table: t.actualTableName,
            pk: t.$primaryKey.length == 1 ? t.$primaryKey.first.name : 'id',
          ),
      ];

  Map<String, TableInfo> get _byName => {for (final t in db.allTables) t.actualTableName: t};

  // ---- outbox ------------------------------------------------------------------

  Future<void> setEnabled(bool on) =>
      db.customStatement("UPDATE sync_meta SET v = '${on ? 1 : 0}' WHERE k = 'enabled'");

  Future<bool> isEnabled() async {
    final r = await db.customSelect("SELECT v FROM sync_meta WHERE k = 'enabled'").getSingleOrNull();
    return r?.read<String>('v') == '1';
  }

  /// Queue every row for upload (first connection of an existing workspace).
  Future<void> enqueueAll() async {
    await db.transaction(() async {
      for (final t in tables) {
        await db.customStatement(
            "INSERT INTO sync_outbox (tbl, rid, op) SELECT '${t.table}', ${t.pk}, 'u' FROM ${t.table}");
      }
    });
  }

  Future<int> pendingCount() async {
    final r = await db.customSelect('SELECT COUNT(DISTINCT tbl || rid) AS n FROM sync_outbox').getSingle();
    return r.read<int>('n');
  }

  /// The oldest [limit] distinct changes waiting to be sent.
  Future<PendingBatch> pending({int limit = 200}) async {
    final rows = await db.customSelect(
      'SELECT tbl, rid, MAX(id) AS last FROM sync_outbox GROUP BY tbl, rid ORDER BY MIN(id) LIMIT ?',
      variables: [Variable.withInt(limit)],
    ).get();
    if (rows.isEmpty) return PendingBatch([], 0);
    final pk = {for (final t in tables) t.table: t.pk};
    final changes = <SyncChange>[];
    var upTo = 0;
    for (final r in rows) {
      final tbl = r.read<String>('tbl');
      final rid = r.read<String>('rid');
      final last = r.read<int>('last');
      if (last > upTo) upTo = last;
      final key = pk[tbl];
      if (key == null) continue;
      final row = await db
          .customSelect('SELECT * FROM $tbl WHERE $key = ?', variables: [Variable.withString(rid)])
          .getSingleOrNull();
      changes.add(row == null
          ? SyncChange(table: tbl, id: rid, deleted: true)
          : SyncChange(table: tbl, id: rid, data: Map<String, Object?>.of(row.data)));
    }
    return PendingBatch(changes, upToId(rows));
  }

  int upToId(List<QueryRow> rows) =>
      rows.fold<int>(0, (a, r) => r.read<int>('last') > a ? r.read<int>('last') : a);

  /// Removes sent changes. Only entries up to [upTo] are cleared, so edits
  /// made while the upload was running stay queued.
  Future<void> acknowledge(PendingBatch b) async {
    if (b.isEmpty) return;
    final keys = [for (final c in b.changes) (c.table, c.id)];
    await db.transaction(() async {
      for (final k in keys) {
        await db.customStatement('DELETE FROM sync_outbox WHERE tbl = ? AND rid = ? AND id <= ?', [k.$1, k.$2, b.upToId]);
      }
    });
  }

  // ---- applying server changes -----------------------------------------------------

  /// Writes changes received from the server without queueing them for upload.
  Future<void> apply(List<SyncChange> changes) async {
    if (changes.isEmpty) return;
    final byName = _byName;
    final pk = {for (final t in tables) t.table: t.pk};
    // Parents and children can arrive in any order.
    await db.customStatement('PRAGMA foreign_keys = OFF');
    try {
      await db.transaction(() async {
        await db.customStatement("UPDATE sync_meta SET v = '1' WHERE k = 'applying'");
        try {
          for (final c in changes) {
            final info = byName[c.table];
            final key = pk[c.table];
            if (info == null || key == null) continue;
            // An edit made on this phone that has not been uploaded yet wins.
            final waiting = await db
                .customSelect('SELECT 1 FROM sync_outbox WHERE tbl = ? AND rid = ? LIMIT 1',
                    variables: [Variable.withString(c.table), Variable.withString(c.id)])
                .getSingleOrNull();
            if (waiting != null) continue;
            if (c.deleted) {
              await db.customStatement('DELETE FROM ${c.table} WHERE $key = ?', [c.id]);
            } else {
              await _upsert(info, key, c);
            }
          }
        } finally {
          await db.customStatement("UPDATE sync_meta SET v = '0' WHERE k = 'applying'");
        }
      });
    } finally {
      await db.customStatement('PRAGMA foreign_keys = ON');
    }
    db.markTablesUpdated(db.allTables);
  }

  Future<void> _upsert(TableInfo info, String pk, SyncChange c) async {
    final known = {for (final col in info.$columns) col.name};
    final data = <String, Object?>{
      for (final e in c.data.entries)
        if (known.contains(e.key)) e.key: e.value,
    };
    data[pk] = c.id;
    // Staff rows arrive without password hashes unless you are the owner.
    if (c.table == 'staff_members') {
      data.putIfAbsent('secret_hash', () => '');
      data.putIfAbsent('salt', () => '');
    }
    final cols = data.keys.toList();
    final marks = List.filled(cols.length, '?').join(', ');
    final updates = cols.where((k) => k != pk).map((k) => '$k = excluded.$k').join(', ');
    final sql = 'INSERT INTO ${c.table} (${cols.join(', ')}) VALUES ($marks) '
        '${updates.isEmpty ? 'ON CONFLICT($pk) DO NOTHING' : 'ON CONFLICT($pk) DO UPDATE SET $updates'}';
    final args = [for (final k in cols) data[k]];
    try {
      await db.customStatement(sql, args);
    } on Exception catch (e) {
      if (!e.toString().contains('UNIQUE')) rethrow;
      await _resolveUnique(c, sql, args);
    }
  }

  /// Two phones created "the same" attendance entry / sheet with different ids.
  /// Every device keeps the one with the smaller id, so they all agree.
  Future<void> _resolveUnique(SyncChange c, String sql, List<Object?> args) async {
    final cols = switch (c.table) {
      'attendance' => ['labour_id', 'site_id', 'date'],
      'attendance_sheets' => ['site_id', 'date'],
      _ => null,
    };
    if (cols == null) return;
    final where = cols.map((k) => '$k = ?').join(' AND ');
    final vals = [for (final k in cols) c.data[k]];
    final clash = await db
        .customSelect('SELECT id FROM ${c.table} WHERE $where', variables: [for (final v in vals) Variable(v)])
        .getSingleOrNull();
    if (clash == null) return;
    final localId = clash.read<String>('id');
    if (c.id.compareTo(localId) < 0) {
      await db.customStatement('DELETE FROM ${c.table} WHERE id = ?', [localId]);
      await db.customStatement(sql, args);
      await _queue(c.table, localId, 'd');
    } else {
      await _queue(c.table, c.id, 'd');
    }
  }

  Future<void> _queue(String table, String id, String op) => db.customStatement(
      "INSERT INTO sync_outbox (tbl, rid, op) SELECT ?, ?, ? WHERE (SELECT v FROM sync_meta WHERE k = 'enabled') = '1'",
      [table, id, op]);

  // ---- bookkeeping --------------------------------------------------------------

  /// Forget everything about cloud sync (when signing out of the cloud).
  Future<void> reset() async {
    await db.customStatement('DELETE FROM sync_outbox');
    await setEnabled(false);
  }
}
