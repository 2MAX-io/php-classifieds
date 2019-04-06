<?php

declare(strict_types=1);

include 'base/header.php';
?>

<div class="container">
    <h1>Installation - Server and PHP requirements</h1>

    <div class="alert alert-info">
        You can find more information about requirements in <a href="https://php-classified-ads.2max.io/requirements/">documentation</a>
    </div>

    <?php foreach ($errors as $error) { ?>
        <div class="alert alert-danger">
            <?php echo e($error); ?>
        </div>
    <?php } ?>

    <div class="mb-2">
        <a href="" class="btn btn-primary">Refresh</a>
    </div>

    <div class="mb-2">
        <a href="<?php echo INSTALL_URL; ?>/filesystem.php" class="btn btn-danger">Skip pre installation verification</a>
    </div>
</div>
