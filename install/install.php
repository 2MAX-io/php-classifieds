<?php

declare(strict_types=1);

use App\Helper\FilePath;
use App\Helper\Random;
use App\Service\User\RoleInterface;
use Symfony\Component\Security\Core\Encoder\Argon2iPasswordEncoder;
use Webmozart\PathUtil\Path;

include 'include/bootstrap.php';

$errors = [];
$projectRootPath = getProjectRootPath();

$configFilePath = Path::canonicalize(FilePath::getProjectDir() . '/zz_engine/.env.local.php');
if (file_exists($configFilePath)) {
    include 'view/already_installed.php';
    exit;
}

if (!empty($_POST)) {
    $pdo = null;
    $dbName = $_POST['db_name'];
    try {
        $pdo = new \PDO("mysql:host={$_POST['db_host']};port={$_POST['db_port']};dbname={$_POST['db_name']}", $_POST['db_user'], $_POST['db_pass'], [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_EMULATE_PREPARES => false,
            \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8, sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));",
        ]);

        if (countTables($dbName) > 0) {
            $errors[] = 'Database is not empty, clear it first';
        }

        $mysqlVersion = $pdo->query('SELECT @@innodb_version')->fetchColumn();
        if ($pdo && version_compare($mysqlVersion, '5.6', '<') ) {
            $errors[] = "Mysql version should be at least 5.6, current MYSQL version is $mysqlVersion (innodb_version)";
        }
    } catch (\Throwable $e) {
        $errors[] = 'Can not connect to database, error: ' . $e->getMessage();
    }

    if (count($errors) === 0) {
        $pdo->beginTransaction();
        try {
            loadSql(__DIR__ . '/data/_schema.sql');
            loadSql(__DIR__ . '/data/_required_data.sql');
            loadSql(__DIR__ . '/data/settings.sql');
            $pdo->exec("UPDATE setting SET last_update_date = '2010-01-01 00:00:00'");

            if ($_POST['load_categories'] ?? null === '1') {
                loadSql(__DIR__ . '/data/example/category.sql');

                if ($_POST['load_custom_fields'] ?? null === '1') {
                    loadSql(__DIR__ . '/data/example/custom_field.sql');

                    if ($_POST['load_listings'] ?? null === '1') {
                        loadSql(__DIR__ . '/data/example/listing_demo_user.sql');
                        loadSql(__DIR__ . '/data/example/listing.large.sql');
                    }
                }
            }

            insertAdmin($_POST['admin_email'], $_POST['admin_password']);
            setEmailSettings($_POST['email_from_address']);

            saveConfig();

            $pdo->commit();
        } catch (\Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }

        $crontabText =<<<EOT
* * * * * $projectRootPath/zz_engine/bin/console app:cron:main >/dev/null 2>&1
* * * * * $projectRootPath/zz_engine/bin/console app:cron:secondary >/dev/null 2>&1
EOT;
        include "view/success.php";
        exit;
    }
}

include 'view/install_form.php';

function loadSql(string $filePath): void {
    global $pdo;
    $currentQuery = '';

    $handle = fopen($filePath, 'r');
    while ($line = fgets($handle)) {
        $currentQuery .= $line;
        if (preg_match('~;$~', $line)) {
            $pdo->query($currentQuery);
            $currentQuery = '';
        }
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

function insertAdmin(string $email, string $password): void {
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

function saveConfig(): void {
    $dbPass = empty($_POST['db_pass']) ? '' : ':' . $_POST['db_pass'];
    dumpConfig([
        'DATABASE_URL' => "mysql://{$_POST['db_user']}{$dbPass}@{$_POST['db_host']}:{$_POST['db_port']}/{$_POST['db_name']}",
        'MAILER_URL' => "smtp://{$_POST['smtp_host']}:{$_POST['smtp_port']}?encryption=ssl&auth_mode=plain&username={$_POST['smtp_username']}&password={$_POST['smtp_password']}",
        'APP_TIMEZONE' => $_POST['app_timezone'],
    ]);
}

function dumpConfig(array $config): void {
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

function setEmailSettings(string $emailFromAddress): void {
    setSetting('emailFromAddress', $emailFromAddress);
    setSetting('emailReplyTo', $emailFromAddress);
}

function setSetting(string $name, string $value): void {
    global $pdo;

    try {
        $sql = <<<'EOF'
    UPDATE setting 
    SET
        value = :value
    WHERE
        name = :name
;

EOF;

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue('name', $name);
        $stmt->bindValue('value', $value);
        $stmt->execute();
    } catch (\Throwable $e) {
        echo "Error while saving the setting with name: $name";
        exit;
    }
}
