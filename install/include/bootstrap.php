<?php

declare(strict_types=1);

use App\Exception\UserVisibleException;
use App\Helper\BoolHelper;
use App\Helper\FilePath;
use Webmozart\PathUtil\Path;

\define('INSTALL_DIR', \dirname(__DIR__));
\define('PROJECT_DIR', \dirname(__DIR__, 2));
\define('PUBLIC_DIR', \dirname(__DIR__, 2));

\ini_set('display_errors', '1');
\error_reporting(-1);
\set_time_limit(600);
\ini_set('memory_limit', '-1');
\ini_set('log_errors', 'On');
\ini_set('error_log', 'data/PHP_native_error_'.\date('Y-m-d').'.log');

/* @noinspection ConstantCanBeUsedInspection - works bad with old PHP versions */
if (\version_compare(\PHP_VERSION, '7.3', '<')) {
    echo e('This app requires PHP 7.3');

    exit;
}

require PROJECT_DIR.'/zz_engine/vendor/autoload.php';

\define('INSTALL_URL', '/'.Path::makeRelative(INSTALL_DIR, $_SERVER['DOCUMENT_ROOT']));

$configFilePath = Path::canonicalize(FilePath::getProjectDir().'/zz_engine/.env.local.php');
if (\file_exists($configFilePath)) {
    include INSTALL_DIR.'/view/already_installed.php';

    exit;
}

function e(string $string): string
{
    return \htmlspecialchars($string, \ENT_QUOTES | \ENT_SUBSTITUTE);
}

function getProjectRootPath(): string
{
    $projectRootPath = Path::canonicalize(__DIR__.'/../../');
    if (!\file_exists($projectRootPath.'/zzzz_2max_io_classified_ads_project_root.txt')) {
        throw new UserVisibleException('Can not find project root');
    }

    return $projectRootPath;
}

function checkedIfTrue($value, $default = false)
{
    $checkedBool = $default;
    if (!empty($value)) {
        $checkedBool = BoolHelper::isTrue($value);
    }

    if (!$checkedBool) {
        return e('');
    }

    return e('checked');
}
