<?php

declare(strict_types=1);

use App\Exception\UserVisibleException;
use Webmozart\PathUtil\Path;

ini_set('display_errors', '1');
error_reporting(-1);
set_time_limit(600);
ini_set('memory_limit', '-1');
ini_set('log_errors', 'On');
ini_set('error_log', 'data/PHP_native_error_'.date('Y-m-d').'.log');

/** @noinspection ConstantCanBeUsedInspection - works bad with old PHP versions */
if (version_compare(PHP_VERSION, '7.3', '<')) {
    echo e('This app requires PHP 7.3');
    exit;
}

require __DIR__ . '/../../zz_engine/vendor/autoload.php';

function e(string $string): string {
    return htmlspecialchars($string, ENT_QUOTES | ENT_SUBSTITUTE);
}

function getProjectRootPath(): string {
    $projectRootPath = Path::canonicalize(__DIR__ . '/../../');
    if (!file_exists($projectRootPath . '/zzzz_2max_io_classified_ads_project_root.txt')) {
        throw new UserVisibleException('Can not find project root');
    }

    return $projectRootPath;
}
