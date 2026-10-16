<?php
declare(strict_types=1);

function db(): PDO {
    static $pdo = null;
    if ($pdo) return $pdo;
    $port = cfg('db_port', null);
    $dsn = sprintf('mysql:host=%s;%sdbname=%s;charset=utf8mb4', cfg('db_host', 'localhost'),
        $port ? "port=$port;" : '', cfg('db_name'));
    try {
        $pdo = new PDO($dsn, (string) cfg('db_user'), (string) cfg('db_pass'), [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    } catch (PDOException $e) {
        error_log('HazriBook DB connect failed: ' . $e->getMessage());
        throw new ApiError(503, 'Database is not reachable.', 'db_unavailable');
    }
    return $pdo;
}
