<?php

declare(strict_types=1);

use App\Helper\FilePath;
use App\Helper\Random;
use App\Helper\Str;
use App\Service\User\RoleInterface;
use App\System\Filesystem\FilesystemChecker;
use Symfony\Component\Security\Core\Encoder\Argon2iPasswordEncoder;
use Webmozart\PathUtil\Path;

ini_set('display_errors', '1');
error_reporting(-1);

if (version_compare(PHP_VERSION, '7.3', '<')) {
    echo 'This app requires PHP 7.3';
    exit;
}

require dirname(__DIR__) . '/zz_engine/vendor/autoload.php';

$errors = [];

if (!empty($_POST)) {
    $pdo = null;
    $dbName = $_POST['db_name'];
    try {
        $pdo = new \PDO("mysql:host={$_POST['db_host']};port={$_POST['db_port']};dbname={$_POST['db_name']}", $_POST['db_user'], $_POST['db_pass'], [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_EMULATE_PREPARES => true,
            \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
        ]);
    } catch (\Throwable $e) {
        $errors[] = 'Can not connect to database';
    }

    if ($pdo && countTables($dbName) > 0) {
        $errors[] = 'Database is not empty, clear it first';
    }

    if (count($errors) === 0) {
        $pdo->beginTransaction();
        try {
            loadSql(__DIR__ . '/data/_schema.sql');
            loadSql(__DIR__ . '/data/_required_data.sql');
            loadSql(__DIR__ . '/data/settings.sql');
            loadSql(__DIR__ . '/data/example/categories.sql');

            $dbPass = empty($_POST['db_pass']) ? '' : ':' . $_POST['db_pass'];
            dumpConfig([
                'DATABASE_URL' => "mysql://{$_POST['db_user']}{$dbPass}@{$_POST['db_host']}:{$_POST['db_port']}/{$_POST['db_name']}",
                'MAILER_URL' => "smtp://{$_POST['smtp_host']}:{$_POST['smtp_port']}?encryption=ssl&auth_mode=plain&username={$_POST['smtp_username']}&password={$_POST['smtp_password']}",
                'APP_TIMEZONE' => $_POST['app_timezone'],
            ]);

            insertAdmin($_POST['admin_email'], $_POST['admin_password']);

            $pdo->commit();
        } catch (\Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }

        include "view/success.php";
        exit;
    }
}

$configPath = Path::canonicalize(FilePath::getProjectDir() . '/zz_engine/.env.local.php');
if (file_exists($configPath)) {
    $errors[] = "It seems like app is already installed, if not remove configuration file $configPath";
}

if (count(FilesystemChecker::creatingDirFailedList())) {
    $errors[] = 'some dirs can not be created';
    exit;
}

if (count(FilesystemChecker::writingFileFailedList())) {
    $errors[] = 'Writing test file to some dirs failed';
    exit;
}

if (count(FilesystemChecker::notWritableFileList())) {
    $errors[] = 'Some files are not writable';
    exit;
}

if (!canWriteToPhpFile()) {
    $errors[] = 'Could not change php file install/data/test.php, check permissions for all files';
    exit;
}

include 'view/install_form.php';

function canWriteToPhpFile(): bool {
    try {
        $filePath = Path::canonicalize(FilePath::getPublicDir() . '/install/data/test.php');
        $content = file_get_contents($filePath);
        $successText = '!!success!!';
        $content = str_replace("{{!REPLACE_THIS!}}", $successText, $content);
        $result = file_put_contents($filePath, $content);

        if (!Str::contains(file_get_contents($filePath), $successText)) {
            return false;
        }

        if (false !== $result && $result > 0) {
            return true;
        }

        return false;
    } catch (\Throwable $e) {
        return false;
    }
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
    return (int) $stmt->fetchColumn();
}

function dumpConfig(array $config) {
    $defaultConfig = [
        'APP_ENV' => 'prod',
        'APP_SECRET' => Random::string(64),
        'APP_LOCALE' => 'en',
        'APP_TIMEZONE' => 'UTC',
        'APP_UPGRADE_DISABLED' => false,
        'APP_UPGRADE_AVAILABLE_CHECK_DISABLED' => false,
        'DATABASE_URL' => '',
        'MAILER_URL' => '',
    ];
    $configToSave = array_replace($defaultConfig, $config);
    $configPhpString = var_export($configToSave, true);

    $vars = <<<EOF
<?php

// This file was generated by running install script

return $configPhpString;

EOF;

    file_put_contents(FilePath::getProjectDir() . '/zz_engine/.env.local.php', $vars, LOCK_EX);

}

function insertAdmin(string $email, string $password) {
    global $pdo;

    try {
        $sql = <<<'EOF'
    INSERT INTO admin 
    SET
        email = :email,
        password = :password,
        roles = :roles,
        enabled = 1
;

EOF;

        $argon2iPasswordEncoder = new Argon2iPasswordEncoder();

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue('email', $email);
        $stmt->bindValue('password', $argon2iPasswordEncoder->encodePassword($password, ''));
        $stmt->bindValue('roles', json_encode([RoleInterface::ROLE_ADMIN]));
        $stmt->execute();
    } catch (\Throwable $e) {
        echo 'Error while creating admin user';
        exit;
    }

}

function escape(string $string): string {
    return htmlspecialchars($string, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
