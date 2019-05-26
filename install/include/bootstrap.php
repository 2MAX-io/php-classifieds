<?php

declare(strict_types=1);

use App\Exception\UserVisibleMessageException;
use Webmozart\PathUtil\Path;

ini_set('display_errors', '1');
error_reporting(-1);
set_time_limit(600);
ini_set('memory_limit', '-1');

if (version_compare(PHP_VERSION, '7.3', '<')) {
    echo 'This app requires PHP 7.3';
    exit;
}

require dirname(dirname(__DIR__)) . '/zz_engine/vendor/autoload.php';

function escape(string $string): string {
    return htmlspecialchars($string, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function getProjectRootPath(): string {
    $projectRootPath = Path::canonicalize(__DIR__ . '/../../');
    if (!file_exists($projectRootPath . '/zzzz_2max_io_classified_ads_project_root.txt')) {
        throw new UserVisibleMessageException('Can not find project root');
    }

    return $projectRootPath;
}
