<?php

declare(strict_types=1);

use App\Helper\FilePath;
use App\Secondary\Filesystem\FilesystemChecker;
use Webmozart\PathUtil\Path;

include 'include/bootstrap.php';

$errors = [];

$configFilePath = Path::canonicalize(FilePath::getProjectDir().'/zz_engine/.env.local.php');
if (\file_exists($configFilePath)) {
    include 'view/already_installed.php';

    exit;
}

$incorrectFilePermissionList = FilesystemChecker::incorrectFilePermissionList();
if (\count($incorrectFilePermissionList)) {
    $errors[] = 'Some files have incorrect permissions';
}

$incorrectDirPermissionList = FilesystemChecker::incorrectDirPermissionList();
if (\count($incorrectDirPermissionList)) {
    $errors[] = 'Some directories have incorrect permissions';
}

$creatingDirFailedList = FilesystemChecker::creatingDirFailedList();
if (\count($creatingDirFailedList)) {
    $errors[] = 'Could not create some test directories';
}

$readingFileFailedList = FilesystemChecker::readingFileFailedList();
if (\count($readingFileFailedList)) {
    $errors[] = 'Could not read from some files';
}

$writingFileFailedList = FilesystemChecker::writingFileFailedList();
if (\count($writingFileFailedList)) {
    $errors[] = 'Could not write from some files';
}

$canWriteToPhpFile = canWriteToPhpFile();
if (!$canWriteToPhpFile) {
    $errors[] = 'Could not change php file install/data/test.php, check permissions for all files';
}

$canExecuteConsole = canExecuteConsole();
if (!$canExecuteConsole) {
    $errors[] = 'Could not execute zz_engine/bin/console, add execution permissions for this file using chmod +x';
}

if (\count($errors) < 1) {
    \header('Location: install.php');

    exit;
}

$projectRootPath = getProjectRootPath();

include 'view/filesystem_problems.php';

function canWriteToPhpFile(): bool
{
    try {
        $filePath = Path::canonicalize(FilePath::getPublicDir().'/install/data/test.php');
        $originalContent = @\file_get_contents($filePath);
        $successText = '!!success!!';
        $newContent = \str_replace('{{!REPLACE_THIS!}}', $successText, $originalContent);
        $writeToFileReturn = @\file_put_contents($filePath, $newContent);
        $fileContentAfterChange = @\file_get_contents($filePath);
        @\file_put_contents($filePath, $originalContent); // restore original content
        if (false === \str_contains($fileContentAfterChange, $successText)) {
            return false;
        }

        if (false === $writeToFileReturn || $writeToFileReturn < 1) {
            return false;
        }

        return true;
    } catch (\Throwable $e) {
        return false;
    }
}

function canExecuteConsole(): bool
{
    return \is_executable(FilePath::getProjectDir().'/zz_engine/bin/console');
}
