<?php

declare(strict_types=1);

use App\Exception\UserVisibleMessageException;
use App\Helper\FilePath;
use App\Helper\Str;
use App\System\Filesystem\FilesystemChecker;
use Webmozart\PathUtil\Path;

include 'include/bootstrap.php';

$incorrectFilePermissionList = FilesystemChecker::incorrectFilePermissionList();
if (count($incorrectFilePermissionList)) {
    $errors[] = 'Some files have incorrect permissions';
}

$incorrectDirPermissionList = FilesystemChecker::incorrectDirPermissionList();
if (count($incorrectDirPermissionList)) {
    $errors[] = 'Some directories have incorrect permissions';
}

$creatingDirFailedList = FilesystemChecker::creatingDirFailedList();
if (count($creatingDirFailedList)) {
    $errors[] = 'Could not create some test directories';
}

$readingFileFailedList = FilesystemChecker::readingFileFailedList();
if (count($readingFileFailedList)) {
    $errors[] = 'Could not read from some files';
}

$writingFileFailedList = FilesystemChecker::writingFileFailedList();
if (count($writingFileFailedList)) {
    $errors[] = 'Could not write from some files';
}

$canWriteToPhpFile = canWriteToPhpFile();
if (!$canWriteToPhpFile) {
    $errors[] = 'Could not change php file install/data/test.php, check permissions for all files';
}

if (count($errors) < 1) {
    header('Redirect: ');
}

$projectRootPath = getProjectRootPath();
include 'view/filesystem_problems.php';

function getProjectRootPath(): string {
    $projectRootPath = Path::canonicalize(__DIR__ . '/../');
    if (!file_exists($projectRootPath . '/zzzz_2max_io_classified_ads_project_root.txt')) {
        throw new UserVisibleMessageException('Can not find project root');
    }

    return $projectRootPath;
}

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
