<title>Install</title>

<link rel="stylesheet" type="text/css"  href="bootstrap.css" />

<div class="container">

    <h1>Installation - Server and PHP requirements</h1>

    <?php foreach ($errors as $error): ?>
        <div class="alert alert-danger">
            <?php echo escape($error) ?>
        </div>
    <?php endforeach; ?>

    <a href="" class="btn btn-primary">Refresh</a>
</div>
