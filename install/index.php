<?php

declare(strict_types=1);

use App\Helper\FilePath;
use App\System\Filesystem\FilesystemChecker;
use Webmozart\PathUtil\Path;

ini_set('display_errors', '1');
error_reporting(-1);

if (version_compare(PHP_VERSION, '7.3', '<')) {
    echo 'This app requires PHP 7.3';
    exit;
}

require dirname(__DIR__) . '/zzzz_engine/vendor/autoload.php';

$configPath = Path::canonicalize(FilePath::getProjectDir() . '/zzzz_engine/.env.local.php');
if (file_exists($configPath)) {
    echo "It seems like app is already installed, if not remove configuration file $configPath";
    exit;
}

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

include 'view/installForm.php';
