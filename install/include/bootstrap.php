<?php

declare(strict_types=1);

ini_set('display_errors', '1');
error_reporting(-1);
set_time_limit(600);
ini_set('memory_limit', '-1');

if (version_compare(PHP_VERSION, '7.3', '<')) {
    echo 'This app requires PHP 7.3';
    exit;
}

require dirname(dirname(__DIR__)) . '/zz_engine/vendor/autoload.php';
