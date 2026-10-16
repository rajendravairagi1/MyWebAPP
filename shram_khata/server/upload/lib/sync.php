<?php
declare(strict_types=1);

/** App tables that may be synced. Names match the app's database tables. */
const SYNC_TABLES = [
    'branches', 'roles', 'staff_members', 'companies', 'sites', 'contracts', 'contract_rates',
    'labours', 'labour_rates', 'labour_documents', 'assignments', 'attendance', 'attendance_sheets',
    'payments', 'invoices', 'invoice_lines', 'invoice_payments', 'audit_logs', 'key_values',
];

/** Only the owner may change these. */
const OWNER_WRITE_ONLY = ['roles', 'staff_members', 'key_values'];

/** Staff never receive these. */
const STAFF_HIDDEN = ['audit_logs'];

/** Fields removed from rows sent to anyone but the owner. */
const SECRET_FIELDS = ['staff_members' => ['secretHash', 'secret_hash', 'salt']];

const MAX_CHANGES_PER_PUSH = 300;
const MAX_RECORD_BYTES = 65536;

function can_read_table(array $u, string $t): bool {
    if ($u['kind'] === 'owner') return true;
    if ($u['kind'] !== 'staff') return false;
    return !in_array($t, STAFF_HIDDEN, true);
}

function can_write_table(array $u, string $t): bool {
    if ($u['kind'] === 'owner') return true;
    if ($u['kind'] !== 'staff') return false;
    return !in_array($t, OWNER_WRITE_ONLY, true);
}

function next_seq(PDO $pdo, string $agencyId): int {
    $pdo->prepare('UPDATE agencies SET seq = LAST_INSERT_ID(seq + 1) WHERE id = ?')->execute([$agencyId]);
    return (int) $pdo->query('SELECT LAST_INSERT_ID()')->fetchColumn();
}

/** POST /v1/sync/push  { "changes": [ {"t":"labours","id":"...","d":{...},"del":false}, ... ] } */
function sync_push(array $u): array {
    if ($u['kind'] === 'manager') throw new ApiError(403, 'This account cannot sync data.', 'forbidden');
    $changes = body()['changes'] ?? null;
    if (!is_array($changes)) throw new ApiError(422, 'changes must be a list.', 'invalid');
    if (count($changes) > MAX_CHANGES_PER_PUSH) {
        throw new ApiError(413, 'Send at most ' . MAX_CHANGES_PER_PUSH . ' changes at a time.', 'too_large');
    }

    $pdo = db();
    $accepted = 0;
    $rejected = [];
    $pdo->beginTransaction();
    try {
        $up = $pdo->prepare(
            'INSERT INTO records (agency_id, tbl, rid, data, deleted, seq, updated_by, updated_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE data = VALUES(data), deleted = VALUES(deleted), seq = VALUES(seq),
                                     updated_by = VALUES(updated_by), updated_at = VALUES(updated_at)'
        );
        foreach ($changes as $c) {
            $t = is_array($c) ? ($c['t'] ?? null) : null;
            $id = is_array($c) ? ($c['id'] ?? null) : null;
            if (!is_string($t) || !in_array($t, SYNC_TABLES, true) || !is_string($id)
                || !preg_match('/^[A-Za-z0-9_.:\-]{1,64}$/', $id)) {
                $rejected[] = ['t' => $t, 'id' => $id, 'reason' => 'invalid'];
                continue;
            }
            if (!can_write_table($u, $t)) {
                $rejected[] = ['t' => $t, 'id' => $id, 'reason' => 'forbidden'];
                continue;
            }
            $del = !empty($c['del']);
            $d = $c['d'] ?? [];
            if (!is_array($d)) {
                $rejected[] = ['t' => $t, 'id' => $id, 'reason' => 'invalid'];
                continue;
            }
            $json = json_encode($del ? new stdClass() : $d, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            if ($json === false || strlen($json) > MAX_RECORD_BYTES) {
                $rejected[] = ['t' => $t, 'id' => $id, 'reason' => 'too_large'];
                continue;
            }
            $up->execute([$u['agency_id'], $t, $id, $json, $del ? 1 : 0, next_seq($pdo, $u['agency_id']), $u['id'], now()]);
            $accepted++;
        }
        $pdo->commit();
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        throw $e;
    }
    $st = $pdo->prepare('SELECT seq FROM agencies WHERE id = ?');
    $st->execute([$u['agency_id']]);
    return ['accepted' => $accepted, 'rejected' => $rejected, 'seq' => (int) $st->fetchColumn()];
}

/** GET /v1/sync/pull?since=N&limit=500 */
function sync_pull(array $u): array {
    if ($u['kind'] === 'manager') throw new ApiError(403, 'This account cannot sync data.', 'forbidden');
    $since = max(0, (int) ($_GET['since'] ?? 0));
    $limit = min(1000, max(1, (int) ($_GET['limit'] ?? 500)));

    $hidden = [];
    foreach (SYNC_TABLES as $t) {
        if (!can_read_table($u, $t)) $hidden[] = $t;
    }
    $sql = 'SELECT tbl, rid, data, deleted, seq, updated_by FROM records WHERE agency_id = ? AND seq > ?';
    $args = [$u['agency_id'], $since];
    if ($hidden) {
        $sql .= ' AND tbl NOT IN (' . implode(',', array_fill(0, count($hidden), '?')) . ')';
        array_push($args, ...$hidden);
    }
    $sql .= ' ORDER BY seq ASC LIMIT ' . $limit;
    $st = db()->prepare($sql);
    $st->execute($args);
    $rows = $st->fetchAll();

    $out = [];
    foreach ($rows as $r) {
        $d = json_decode($r['data'], true);
        if (!is_array($d)) $d = [];
        if ($u['kind'] !== 'owner' && isset(SECRET_FIELDS[$r['tbl']])) {
            foreach (SECRET_FIELDS[$r['tbl']] as $f) unset($d[$f]);
        }
        $out[] = [
            't' => $r['tbl'], 'id' => $r['rid'], 'd' => $d ?: new stdClass(),
            'del' => (bool) $r['deleted'], 'seq' => (int) $r['seq'], 'by' => $r['updated_by'],
        ];
    }
    $last = $rows ? (int) $rows[count($rows) - 1]['seq'] : $since;
    return ['changes' => $out, 'seq' => $last, 'more' => count($rows) === $limit];
}
