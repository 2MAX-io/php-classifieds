<?php

declare(strict_types=1);

\ini_set('display_errors', '1');
\error_reporting(-1);

include 'include/bootstrap.php';

$errors = [];

/* @noinspection ConstantCanBeUsedInspection */
if (\version_compare(\PHP_VERSION, '7.3', '<')) {
    $phpVersion = \PHP_VERSION;
    $errors[] = "This app requires at least: PHP 7.3, current PHP version is: {$phpVersion}";
    $errors[] = 'Most hosts support current PHP versions, but not by default. 
        Check our documentation, your hosting documentation or contact your hosting support to set PHP to at least 7.3 for this app.
    ';
}

if (!\extension_loaded('pdo')) {
    $errors[] = 'This app requires pdo extension';
}

if (!\extension_loaded('pdo_mysql')) {
    $errors[] = 'This app requires pdo_mysql extension';
}

if (!\extension_loaded('mbstring')) {
    $errors[] = 'This app requires mbstring extension';
}

if (!\extension_loaded('openssl')) {
    $errors[] = 'This app requires openssl extension';
}

if (!\extension_loaded('dom')) {
    $errors[] = 'This app requires dom / xml extension';
}

if (!\extension_loaded('gd')) {
    $errors[] = 'This app requires gd extension';
}

if (!\extension_loaded('intl')) {
    $errors[] = 'This app requires intl extension';
}

if (!\extension_loaded('pcre')) {
    $errors[] = 'This app requires pcre extension';
}

if (!\extension_loaded('ctype')) {
    $errors[] = 'This app requires ctype extension';
}

if (!\extension_loaded('fileinfo')) {
    $errors[] = 'This app requires fileinfo extension';
}

if (!\extension_loaded('json')) {
    $errors[] = 'This app requires json extension';
}

if (!\extension_loaded('iconv')) {
    $errors[] = 'This app requires iconv extension';
}

if (!\extension_loaded('simplexml')) {
    $errors[] = 'This app requires simplexml extension';
}

if (!\extension_loaded('session')) {
    $errors[] = 'This app requires session extension';
}

if (!\extension_loaded('tokenizer')) {
    $errors[] = 'This app requires tokenizer extension';
}

if (!\extension_loaded('curl')) {
    $errors[] = 'This app requires curl extension';
}

if (!\extension_loaded('libxml')) {
    $errors[] = 'This app requires libxml extension';
}

if (!\extension_loaded('xml')) {
    $errors[] = 'This app requires xml extension';
}

if (!\extension_loaded('xmlwriter')) {
    $errors[] = 'This app requires xmlwriter extension';
}

if (!\extension_loaded('SPL')) {
    $errors[] = 'This app requires SPL extension';
}

if (0 === \count($errors)) {
    \header('Location: filesystem.php');

    exit;
}

include 'view/php_requirements.php';
