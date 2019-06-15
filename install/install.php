<?php

declare(strict_types=1);

use App\Helper\FilePath;
use App\Helper\Random;
use App\Service\User\RoleInterface;
use Symfony\Component\Security\Core\Encoder\SodiumPasswordEncoder;
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
        $pdo = new \PDO("mysql:host={$_POST['db_host']};port={$_POST['db_port']};dbname={$_POST['db_name']};charset=utf8mb4", $_POST['db_user'], $_POST['db_pass'], [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_EMULATE_PREPARES => false,
        ]);

        if (countTables($dbName) > 0) {
            $errors[] = 'Database is not empty, clear it first';
        }

        $mysqlVersion = $pdo->query('SELECT @@innodb_version')->fetchColumn();
        if ($pdo && version_compare($mysqlVersion, '5.6', '<') ) {
            $errors[] = "MYSQL version should be at least 5.6, current MYSQL version is $mysqlVersion (innodb_version)";
        }

        $sqlMode = $pdo->query('SELECT @@sql_mode')->fetchColumn();
        if ($pdo && false !== \strpos($sqlMode, 'ONLY_FULL_GROUP_BY')) {
            $errors[] = 'MYSQL: disable sql_mode: ONLY_FULL_GROUP_BY';
            $errors[] = "current sql_mode = $sqlMode";
        }
    } catch (\Throwable $e) {
        $errors[] = 'Can not connect to database, error: ' . $e->getMessage();
    }

    if (!preg_match("~(\w{8})-((\w{4})-){3}(\w{12})~m", $_POST['license'] ?? '')) {
        $errors[] = 'Please enter valid license, current license is not valid';
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

                        $stmt = $pdo->prepare(/** @lang MySQL */ 'UPDATE listing SET valid_until_date = :validUntilDate WHERE 1');
                        $stmt->bindValue('validUntilDate', date('Y-m-d 23:59:59', time() + 3600*24*7));
                        $stmt->execute();
                    }
                }
            }

            if ($_POST['load_pages'] ?? null === '1') {
                loadSql(__DIR__ . '/data/example/page.sql');
            }

            insertAdmin($_POST['admin_email'], $_POST['admin_password']);
            setEmailSettings($_POST['email_from_address']);
            setLicense($_POST['license']);

            $pdo->exec("UPDATE setting SET last_update_date = '2010-01-01 00:00:00'");
            $pdo->exec("UPDATE listing SET order_by_date = '2010-01-01 00:00:00'");
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

        $argon2iPasswordEncoder = new SodiumPasswordEncoder();

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

function setLicense(string $license): void {
    setSetting('license', $license);
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
