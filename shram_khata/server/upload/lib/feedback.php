<?php
declare(strict_types=1);

/** Creates the message tables on first use, so no manual database step is needed. */
function ensure_feedback_tables(): void {
    static $done = false;
    if ($done) return;
    $pdo = db();
    $pdo->exec('CREATE TABLE IF NOT EXISTS feedback (
        id CHAR(36) NOT NULL PRIMARY KEY, agency_id CHAR(36) NOT NULL, user_id CHAR(36) NOT NULL,
        company_id CHAR(36) NULL, labour_id CHAR(36) NULL, message TEXT NOT NULL, created_at DATETIME NOT NULL,
        KEY ix_fb_agency (agency_id, created_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci');
    $pdo->exec('CREATE TABLE IF NOT EXISTS feedback_reads (
        feedback_id CHAR(36) NOT NULL, user_id CHAR(36) NOT NULL, read_at DATETIME NOT NULL,
        PRIMARY KEY (feedback_id, user_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci');
    $done = true;
}

/** POST /v1/feedback  { "message": "...", "labourId": "optional" } — managers only. */
function feedback_create(array $u): array {
    ensure_feedback_tables();
    if ($u['kind'] !== 'manager') throw new ApiError(403, 'Only company managers can send feedback.', 'forbidden');
    $b = body();
    $message = str_field($b, 'message', 2000);
    $labour = $b['labourId'] ?? null;
    if ($labour !== null && !(is_string($labour) && preg_match('/^[A-Za-z0-9_.:\-]{1,64}$/', $labour))) {
        throw new ApiError(422, 'labourId is invalid.', 'invalid');
    }
    // Slow down accidental floods: at most 30 messages an hour per manager.
    $st = db()->prepare('SELECT COUNT(*) FROM feedback WHERE user_id = ? AND created_at >= ?');
    $st->execute([$u['id'], gmdate('Y-m-d H:i:s', time() - 3600)]);
    if ((int) $st->fetchColumn() >= 30) throw new ApiError(429, 'Too many messages. Please try again later.', 'rate_limited');

    $id = uuid4();
    db()->prepare('INSERT INTO feedback (id, agency_id, user_id, company_id, labour_id, message, created_at) VALUES (?, ?, ?, ?, ?, ?, ?)')
        ->execute([$id, $u['agency_id'], $u['id'], $u['scope_company_id'], $labour, $message, now()]);
    return ['id' => $id];
}

/**
 * GET /v1/feedback?since=<datetime>
 * Owner and staff see every message of the agency; a manager sees only their own.
 */
function feedback_list(array $u): array {
    ensure_feedback_tables();
    $args = [$u['agency_id'], $u['id']];
    $sql = 'SELECT f.id, f.user_id, f.company_id, f.labour_id, f.message, f.created_at, m.name AS from_name,
                   (r.feedback_id IS NOT NULL) AS is_read
              FROM feedback f
              JOIN users m ON m.id = f.user_id
              LEFT JOIN feedback_reads r ON r.feedback_id = f.id AND r.user_id = ?
             WHERE f.agency_id = ?';
    $args = [$u['id'], $u['agency_id']];
    if ($u['kind'] === 'manager') {
        $sql .= ' AND f.user_id = ?';
        $args[] = $u['id'];
    }
    $sql .= ' ORDER BY f.created_at DESC LIMIT 200';
    $st = db()->prepare($sql);
    $st->execute($args);
    $items = array_map(fn($r) => [
        'id' => $r['id'], 'fromName' => $r['from_name'], 'userId' => $r['user_id'],
        'companyId' => $r['company_id'], 'labourId' => $r['labour_id'],
        'message' => $r['message'], 'createdAt' => $r['created_at'] . 'Z', 'isRead' => (bool) $r['is_read'],
    ], $st->fetchAll());
    $unread = count(array_filter($items, fn($i) => !$i['isRead']));
    return ['items' => $items, 'unread' => $u['kind'] === 'manager' ? 0 : $unread];
}

/** POST /v1/feedback/read  { "ids": [...] } — marks messages as seen by this person. */
function feedback_mark_read(array $u): array {
    ensure_feedback_tables();
    $ids = body()['ids'] ?? [];
    if (!is_array($ids) || count($ids) > 200) throw new ApiError(422, 'ids must be a short list.', 'invalid');
    $ins = db()->prepare('INSERT IGNORE INTO feedback_reads (feedback_id, user_id, read_at)
                          SELECT f.id, ?, ? FROM feedback f WHERE f.id = ? AND f.agency_id = ?');
    foreach ($ids as $id) {
        if (valid_uuid(is_string($id) ? $id : null)) $ins->execute([$u['id'], now(), $id, $u['agency_id']]);
    }
    return [];
}
