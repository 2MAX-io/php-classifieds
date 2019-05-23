<?php

declare(strict_types=1);

use App\System\Filesystem\FilesystemChecker;

if (version_compare(PHP_VERSION, '7.3', '<')) {
    echo 'This app requires PHP 7.3';
    exit;
}

require dirname(__DIR__) . '/symfony/vendor/autoload.php';

if (count(FilesystemChecker::notWritableFileList())) {
    echo 'Some files are not writable';
    exit;
}

if (count(FilesystemChecker::creatingDirFailedList())) {
    echo 'some dirs can not be created';
    exit;
}

if (count(FilesystemChecker::writingFileFailedList())) {
    echo 'Writing test file to some dirs failed';
    exit;
}

$dbName = 'dev_test';
$pdo = new \PDO("mysql:host=mysql;dbname=$dbName", 'root', '', [
    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
    \PDO::ATTR_EMULATE_PREPARES => true,
    \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
]);

if (countTables($dbName) > 0) {
    echo 'Database is not empty, clear it first';
    exit;
}

$pdo->beginTransaction();
try {
    loadSql(__DIR__ . '/data/_schema.sql');
    loadSql(__DIR__ . '/data/_required_data.sql');
    loadSql(__DIR__ . '/data/settings.sql');
    loadSql(__DIR__ . '/data/example/categories.sql');
} catch (\Throwable $e) {
    $pdo->rollBack();
    throw $e;
}

function loadSql(string $filePath) {
    global $pdo;
    $sqlFileContent = file_get_contents($filePath);

    $stmt = $pdo->query($sqlFileContent);

    while ($stmt->nextRowset()) {
        continue;
    }
}

function countTables(string $dbName): int {
    global $pdo;

    $stmt = $pdo->prepare(
    /** @lang MySQL */ 'SELECT count(1) FROM information_schema.tables where table_schema=:dbName;'
    );
    $stmt->bindValue('dbName', $dbName);
    $stmt->execute();
    return (int) $stmt->fetch();
}
