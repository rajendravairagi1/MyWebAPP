<?php
/**
 * Pure-PHP database backup (no `mysqldump` shell dependency - shared
 * hosting usually disables exec()/shell_exec()). Dumps schema + data for
 * every table into a single timestamped .sql file under /backups.
 * Old backups beyond BACKUP_KEEP are pruned automatically.
 */
require_once __DIR__ . '/db.php';

const BACKUP_KEEP = 14; // keep last 14 daily backups

function run_backup(): string
{
    $pdo = db();
    $dir = __DIR__ . '/../backups/';
    if (!is_dir($dir)) mkdir($dir, 0755, true);

    $file = $dir . 'backup-' . date('Y-m-d_His') . '.sql';
    $fh = fopen($file, 'w');
    fwrite($fh, "-- " . APP_NAME . " backup - " . date('Y-m-d H:i:s') . "\nSET FOREIGN_KEY_CHECKS=0;\n\n");

    $tables = $pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
    foreach ($tables as $table) {
        $createRow = $pdo->query('SHOW CREATE TABLE `' . $table . '`')->fetch();
        fwrite($fh, "DROP TABLE IF EXISTS `$table`;\n" . $createRow['Create Table'] . ";\n\n");

        $rowCount = 0;
        $stmt = $pdo->query('SELECT * FROM `' . $table . '`');
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $cols = array_map(fn($c) => '`' . $c . '`', array_keys($row));
            $vals = array_map(function ($v) use ($pdo) {
                return $v === null ? 'NULL' : $pdo->quote((string)$v);
            }, array_values($row));
            fwrite($fh, "INSERT INTO `$table` (" . implode(',', $cols) . ") VALUES (" . implode(',', $vals) . ");\n");
            $rowCount++;
        }
        fwrite($fh, "\n");
    }
    fwrite($fh, "SET FOREIGN_KEY_CHECKS=1;\n");
    fclose($fh);

    prune_old_backups($dir);
    return basename($file);
}

function prune_old_backups(string $dir): void
{
    $files = glob($dir . 'backup-*.sql');
    if (!$files) return;
    usort($files, fn($a, $b) => filemtime($b) - filemtime($a));
    foreach (array_slice($files, BACKUP_KEEP) as $old) {
        @unlink($old);
    }
}

function list_backups(): array
{
    $dir = __DIR__ . '/../backups/';
    $files = glob($dir . 'backup-*.sql') ?: [];
    usort($files, fn($a, $b) => filemtime($b) - filemtime($a));
    return array_map(fn($f) => ['name' => basename($f), 'size' => filesize($f), 'time' => filemtime($f)], $files);
}
