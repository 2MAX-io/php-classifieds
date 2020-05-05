<title>Install</title>

<!--suppress HtmlUnknownTarget -->
<link rel="stylesheet" type="text/css" href="bootstrap.css" />

<div class="container">

    <h1>Installation - Server and PHP requirements</h1>

    <div class="alert alert-info">
        You can find more information about requirements in <a href="https://documentation.2max.io/display/CLAS/Requirements">documentation</a>
    </div>

    <?php foreach ($errors as $error): ?>
        <div class="alert alert-danger">
            <?php echo e($error) ?>
        </div>
    <?php endforeach; ?>

    <a href="" class="btn btn-primary">Refresh</a>
</div>
