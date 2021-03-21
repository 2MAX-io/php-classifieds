<?php

declare(strict_types=1);

$pageTitle = 'Install success';

include 'base/header.php';
?>

<div class="container">
    <h1>Installation</h1>

    <div class="alert alert-success">
        Installation has been successful.
        Steps to take after installation can be found in <a href="https://php-classified-ads.2max.io/installation/">documentation</a>
    </div>

    <div class="alert alert-info">
        Remove installation directory
    </div>

    <div class="alert alert-info">
        Set up cron

        <textarea class="w-100 form-text cron-textarea"><?php echo e($crontabText ?? ''); ?></textarea>
    </div>

    <div class="alert alert-info">
        Check if emails are send correctly by registering an user
    </div>

    <!--suppress HtmlUnknownTarget -->
    <a href="../admin/red5" class="btn btn-primary">Go to admin page</a>
</div>
