<?php

declare(strict_types=1);

use App\Enum\UserRoleEnum;
use App\Helper\FilePath;
use App\Helper\RandomHelper;
use App\Service\System\Signature\LicenseValidService;
use Symfony\Component\Security\Core\Encoder\NativePasswordEncoder;
use Symfony\Component\Security\Core\Encoder\SodiumPasswordEncoder;
use Webmozart\PathUtil\Path;

include 'include/bootstrap.php';

$errors = [];
$projectRootPath = getProjectRootPath();

$configFilePath = Path::canonicalize(FilePath::getProjectDir().'/zz_engine/.env.local.php');
if (\file_exists($configFilePath)) {
    include 'view/already_installed.php';

    exit;
}

if (isset($_GET['execute-no-interaction'])) {
    $_POST = include INSTALL_DIR.'/include/install_default_config.php';
}

if (!empty($_POST)) {
    $pdo = null;
    $dbName = $_POST['db_name'];

    try {
        $pdo = new \PDO(
            "mysql:host={$_POST['db_host']};port={$_POST['db_port']};dbname={$_POST['db_name']};charset=utf8mb4",
            $_POST['db_user'],
            $_POST['db_pass'],
            [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );

        if (countTables($dbName) > 0) {
            $errors[] = 'Database is not empty, clear it first';
        }

        $mysqlVersion = $pdo->query('SELECT @@innodb_version')->fetchColumn();
        if ($pdo && \version_compare($mysqlVersion, '5.6', '<')) {
            $errors[] = "MYSQL version should be at least 5.6, current MYSQL version is {$mysqlVersion} (innodb_version)";
        }

        $sqlMode = $pdo->query('SELECT @@sql_mode')->fetchColumn();
        if ($pdo && false !== \strpos($sqlMode, 'ONLY_FULL_GROUP_BY')) {
            $errors[] = 'MYSQL: disable sql_mode: ONLY_FULL_GROUP_BY';
            $errors[] = "current sql_mode = {$sqlMode}";
        }
    } catch (\Throwable $e) {
        $errors[] = 'Can not connect to database, error: '.$e->getMessage();
    }

    if (0 === \count($errors)) {
        $pdo->beginTransaction();

        try {
            loadSql(__DIR__.'/data/_schema.sql');
            loadSql(__DIR__.'/data/_required_data.sql');
            loadSql(__DIR__.'/data/settings.sql');
            $pdo->exec("UPDATE setting SET last_update_date = '2010-01-01 00:00:00' WHERE 1");

            if ($_POST['load_categories'] ?? false) {
                loadSql(__DIR__.'/data/example/category.sql');

                if ($_POST['load_custom_fields'] ?? false) {
                    loadSql(__DIR__.'/data/example/custom_field.sql');

                    if ($_POST['load_listings'] ?? false) {
                        loadSql(__DIR__.'/data/example/listing_demo_user.sql');
                        if (\file_exists(__DIR__.'/data/example/large_git_ignored/listing.sql')) {
                            loadSql(__DIR__.'/data/example/large_git_ignored/listing.sql');
                        }
                        if (\file_exists(__DIR__.'/data/example/large_git_ignored/listing_file.sql')) {
                            loadSql(__DIR__.'/data/example/large_git_ignored/listing_file.sql');
                        }
                        if (\file_exists(__DIR__.'/data/example/large_git_ignored/listing_custom_field_value.sql')) {
                            loadSql(__DIR__.'/data/example/large_git_ignored/listing_custom_field_value.sql');
                        }

                        $stmt = $pdo->prepare(/* @lang MySQL */ 'UPDATE listing SET valid_until_date = :validUntilDate WHERE 1');
                        $stmt->bindValue('validUntilDate', \date('Y-m-d 23:59:59', \time() + 3600 * 24 * 7));
                        $stmt->execute();
                    }
                }
            }

            if ($_POST['load_pages'] ?? false) {
                loadSql(__DIR__.'/data/example/page.sql');
            }

            insertAdmin($_POST['admin_email'], $_POST['admin_password']);
            setEmailSettings($_POST['email_from_address']);
            setLicense(\trim($_POST['license']));

            $pdo->exec("UPDATE setting SET last_update_date = '2010-01-01 00:00:00' WHERE 1");
            $pdo->exec("UPDATE listing SET order_by_date = '2010-01-01 00:00:00' WHERE 1");
            $pdo->exec('ALTER TABLE user AUTO_INCREMENT = 1001;');
            saveConfig();

            if ($pdo->inTransaction()) {
                $pdo->commit();
            }
        } catch (\Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            throw $e;
        }

        $crontabText = <<<EOT
* * * * * {$projectRootPath}/zz_engine/bin/console app:cron:main >/dev/null 2>&1
* * * * * {$projectRootPath}/zz_engine/bin/console messenger:consume async --time-limit=60 --memory-limit=128M >/dev/null 2>&1
* * * * * {$projectRootPath}/zz_engine/bin/console messenger:consume one_at_time --time-limit=60 --limit=1 >/dev/null 2>&1
EOT;

        include 'view/success.php';

        exit;
    }
}

$formDefaultValue = include INSTALL_DIR.'/include/install_default_config.php';

$isDockerDevEnvironment = \str_contains($_SERVER['SERVER_ADDR'], '192.168.205');
if ($isDockerDevEnvironment) {
    $formDefaultValue['db_host'] = 'mysql';
    $formDefaultValue['db_port'] = '3306';
    $formDefaultValue['db_name'] = 'classifieds';
    $formDefaultValue['db_user'] = 'classifieds';
    $formDefaultValue['db_pass'] = 'classifieds';
    $formDefaultValue['smtp_host'] = 'mailhog';
    $formDefaultValue['smtp_port'] = '1025';
    $formDefaultValue['smtp_username'] = '2max.io@2max.io';
    $formDefaultValue['smtp_password'] = 'passwordHere';
    $formDefaultValue['email_from_address'] = $formDefaultValue['smtp_username'];
    $formDefaultValue['app_timezone'] = 'UTC';
    $formDefaultValue['admin_email'] = 'admin@2max.io';
    $formDefaultValue['admin_password'] = 'demo';
    $formDefaultValue['load_categories'] = true;
    $formDefaultValue['load_custom_fields'] = '';
    $formDefaultValue['load_listings'] = '';
    $formDefaultValue['load_pages'] = '';
}

include 'view/install_form.php';

function loadSql(string $filePath): void
{
    global $pdo;
    $currentQuery = '';

    $handle = \fopen($filePath, 'rb');
    if (!$handle) {
        throw new \RuntimeException('could not get file handle');
    }
    while ($line = \fgets($handle)) {
        $currentQuery .= $line;
        if (\preg_match('~;$~', $line)) {
            $pdo->exec($currentQuery);
            $currentQuery = '';
        }
    }
}

function countTables(string $dbName): int
{
    global $pdo;

    $stmt = $pdo->prepare(
    /* @lang MySQL */
        'SELECT count(1) FROM information_schema.tables where table_schema=:dbName;'
    );
    $stmt->bindValue('dbName', $dbName);
    $stmt->execute();

    return (int) $stmt->fetchColumn();
}

function insertAdmin(string $email, string $password): void
{
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

        if (SodiumPasswordEncoder::isSupported()) {
            $passwordEncoder = new SodiumPasswordEncoder();
        } else {
            /**
             * used as fallback if Libsodium not installed, which is the case for some shared hosting providers
             */
            $passwordEncoder = new NativePasswordEncoder();
        }

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue('email', $email);
        $stmt->bindValue('password', $passwordEncoder->encodePassword($password, ''));
        $stmt->bindValue('roles', \json_encode([UserRoleEnum::ROLE_ADMIN]));
        $stmt->execute();
    } catch (\Throwable $e) {
        echo e('Error while creating admin user: '.$e->getMessage());

        exit;
    }
}

function saveConfig(): void
{
    $dbPass = empty($_POST['db_pass']) ? '' : ':'.\urlencode($_POST['db_pass']);
    $smtpPass = \urlencode($_POST['smtp_password']);
    saveConfigToFile([
        'DATABASE_URL' => "mysql://{$_POST['db_user']}{$dbPass}@{$_POST['db_host']}:{$_POST['db_port']}/{$_POST['db_name']}",
        'MAILER_URL' => "selfsignedsmtp://{$_POST['smtp_host']}:{$_POST['smtp_port']}?encryption=ssl&auth_mode=plain&username={$_POST['smtp_username']}&password={$smtpPass}",
        'APP_TIMEZONE' => $_POST['app_timezone'],
    ]);
}

function saveConfigToFile(array $config): void
{
    $requestUriDir = \dirname($_SERVER['REQUEST_URI']);
    $defaultConfig = [
        'DATABASE_URL' => '',
        'MAILER_URL' => '',
        'APP_HTTP_HOST' => $_SERVER['HTTP_HOST'],
        'APP_HTTP_SCHEME' => $_SERVER['REQUEST_SCHEME'],
        'APP_HTTP_BASE_URL' => \substr($requestUriDir, 0, \strpos($requestUriDir, '/install') ?: 0),
        'APP_ENV' => 'prod',
        'APP_SECRET' => RandomHelper::string(64),
        'APP_LOCALE' => 'en',
        'APP_TIMEZONE' => 'UTC',

        'APP_UPGRADE_DISABLED' => false,
        'APP_UPGRADE_AVAILABLE_CHECK_DISABLED' => false,
        'APP_NOT_PUBLIC_URL_SECRET' => RandomHelper::string(64),
    ];
    $configToSave = \array_replace($defaultConfig, $config);
    $configPhpString = \var_export($configToSave, true);

    $configFileContent = <<<EOF
<?php

declare(strict_types=1);

// This file was generated by running install script

return {$configPhpString};

EOF;

    \file_put_contents(FilePath::getProjectDir().'/zz_engine/.env.local.php', $configFileContent, \LOCK_EX);
}

function setEmailSettings(string $emailFromAddress): void
{
    setSetting('emailFromAddress', $emailFromAddress);
    setSetting('emailReplyTo', $emailFromAddress);
}

function setLicense(string $licenseText): void
{
    setSetting('license', $licenseText);
    if (LicenseValidService::isLicenseValid($licenseText)) {
        setSetting('licenseValid', '1');
    }
}

function setSetting(string $name, string $value): void
{
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
        echo e("Error while saving the setting with name: {$name}, {$e->getMessage()}");

        exit;
    }
}
