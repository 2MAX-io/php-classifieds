<?php /** @noinspection PhpMissingStrictTypesDeclarationInspection */

ini_set('display_errors', '1');
error_reporting(-1);

$errors = array();

if (version_compare(PHP_VERSION, '7.3', '<')) {
    $phpVersion = PHP_VERSION;
    $errors[] = "This app requires at least: PHP 7.3, current PHP version is: $phpVersion";
    $errors[] = "Most hosts support current PHP versions, but not by default. Consult with hosting documentation or hosting support to set PHP to at least 7.3 for this app.";
    include 'view/php_requirements.php';
    exit;
}

if (count($errors) === 0) {
    header('Location: filesystem.php');
    exit;
}

function escape($string) {
    return htmlspecialchars($string, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
