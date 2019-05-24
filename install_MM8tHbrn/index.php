<?php

declare(strict_types=1);

use App\Helper\FilePath;
use App\System\Filesystem\FilesystemChecker;
use Webmozart\PathUtil\Path;

if (version_compare(PHP_VERSION, '7.3', '<')) {
    echo 'This app requires PHP 7.3';
    exit;
}

require dirname(__DIR__) . '/symfony/vendor/autoload.php';

$configPath = Path::canonicalize(FilePath::getProjectDir() . '/symfony/.env.local.php');
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
