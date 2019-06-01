<?php

declare(strict_types=1);

use App\Helper\FilePath;
use App\Helper\Str;
use App\System\Filesystem\FilesystemChecker;
use Webmozart\PathUtil\Path;

include 'include/bootstrap.php';

$errors = [];

$configFilePath = Path::canonicalize(FilePath::getProjectDir() . '/zz_engine/.env.local.php');
if (\file_exists($configFilePath)) {
    include 'view/already_installed.php';
    exit;
}

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
    header('Location: install.php');
    exit;
}

$projectRootPath = getProjectRootPath();
include 'view/filesystem_problems.php';

function canWriteToPhpFile(): bool {
    try {
        $filePath = Path::canonicalize(FilePath::getPublicDir() . '/install/data/test.php');
        $originalContent = @file_get_contents($filePath);
        $successText = '!!success!!';
        $newContent = str_replace("{{!REPLACE_THIS!}}", $successText, $originalContent);
        $result = @file_put_contents($filePath, $newContent);

        if (!Str::contains(@file_get_contents($filePath), $successText)) {
			@file_put_contents($filePath, $originalContent); // restore original content
            return false;
        }

        if (false !== $result && $result > 0) {
			@file_put_contents($filePath, $originalContent); // restore original content
            return true;
        }

		@file_put_contents($filePath, $originalContent); // restore original content
        return false;
    } catch (\Throwable $e) {
        return false;
    }
}
