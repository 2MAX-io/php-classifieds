<title>Install</title>

<!--suppress HtmlUnknownTarget -->
<link rel="stylesheet" type="text/css" href="bootstrap.css" />

<div class="container">

    <h1>Already installed</h1>

    <div class="alert alert-danger">
        It seems like app is already installed.
        If everything is fine, please remove installation directory.
    </div>

    <div class="alert alert-info">
        To rerun install, you need to remove configuration file <?php echo $configFilePath ?> and clear app database.
    </div>

</div>
