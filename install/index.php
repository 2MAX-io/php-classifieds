<?php

declare(strict_types=1);

use App\Helper\FilePath;
use App\Helper\Str;
use App\System\Filesystem\FilesystemChecker;
use Webmozart\PathUtil\Path;

ini_set('display_errors', '1');
error_reporting(-1);

if (version_compare(PHP_VERSION, '7.3', '<')) {
    echo 'This app requires PHP 7.3';
    exit;
}

require dirname(__DIR__) . '/zz_engine/vendor/autoload.php';

$configPath = Path::canonicalize(FilePath::getProjectDir() . '/zz_engine/.env.local.php');
if (file_exists($configPath)) {
    echo "It seems like app is already installed, if not remove configuration file $configPath";
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

if (count(FilesystemChecker::notWritableFileList())) {
    echo 'Some files are not writable';
    exit;
}

if (!canWriteToPhpFile()) {
    echo 'Could not change php file install/data/test.php, check permissions for all files';
    exit;
}

include 'view/installForm.php';

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
