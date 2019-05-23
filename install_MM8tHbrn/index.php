<?php

declare(strict_types=1);

use App\System\Filesystem\FilesystemChecker;

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

$pdo = new \PDO('mysql:host=mysql;dbname=dev_test', 'root', '', [
    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
    \PDO::ATTR_EMULATE_PREPARES => true,
    \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
]);


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
    $sqlList = explode("\n", $sqlFileContent);

    $stmt = $pdo->query($sqlFileContent);

    while ($stmt->nextRowset()) {
        continue;
    }
}
