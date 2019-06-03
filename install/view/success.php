<title>Install success</title>

<link rel="stylesheet" type="text/css"  href="bootstrap.css" />

<div class="container">

    <h1>Installation</h1>

    <div class="alert alert-success">
        Installation has been successful.
        Steps to take after installation can be found in <a href="https://documentation.2max.io/display/CLAS/Installation">documentation</a>
    </div>

    <div class="alert alert-info">
        Remove installation directory
    </div>

    <div class="alert alert-info">
        Set up cron

        <textarea class="w-100 form-text"><?php echo $crontabText ?></textarea>
    </div>

    <div class="alert alert-info">
        Check if emails are send correctly by registering an user
    </div>

    <a href="../admin/red5" class="btn btn-primary">Go to admin page</a>

</div>
