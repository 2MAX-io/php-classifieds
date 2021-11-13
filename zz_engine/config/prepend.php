<?php

declare(strict_types=1);

// always prepend this code before autoloader

ini_set('log_errors', 'On');
ini_set('error_log', 'zz_engine/var/log/PHP_native_error_'.date('Y-m-d').'.log');

date_default_timezone_set('UTC');
