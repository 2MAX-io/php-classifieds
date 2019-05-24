<title>Install</title>

<link rel="stylesheet" type="text/css"  href="bootstrap.css" />

<div class="container">

    <form action="install.php" method="post">
        <h1>Installation</h1>

        <div class="mb-4">
            <h3>Database:</h3>

            <div class="form-group">
                <label>DB host</label>
                <input name="db_host" value="localhost" type="text" placeholder="DB host" class="form-control" required>
            </div>

            <div class="form-group">
                <label>DB port</label>
                <input name="db_port" value="3306" type="text" placeholder="DB port" class="form-control" required>
            </div>

            <div class="form-group">
                <label>DB database name</label>
                <input name="db_name" type="text" placeholder="database_name" class="form-control" required>
            </div>

            <div class="form-group">
                <label>DB username</label>
                <input name="db_user" type="text" placeholder="username" class="form-control" required>
            </div>

            <div class="form-group">
                <label>DB password</label>
                <input name="db_pass" type="text" placeholder="password" class="form-control">
            </div>
        </div>

        <div class="mb-4">
            <h3>Other settings</h3>

            <div class="form-group">
                <label>Timezone</label>
                <select name="app_timezone" type="text" class="form-control">
                    <?php foreach (\DateTimeZone::listIdentifiers() as $timezone): ?>
                    <option <?php echo $timezone === 'UTC' ? 'selected' : '' ?> value="<?php echo $timezone ?>"><?php echo $timezone ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="mb-4">
            <h3>Sending Emails using SMTP</h3>

            <div class="form-group">
                <label>SMTP host</label>
                <input name="smtp_host" type="text" placeholder="mail.host.com" class="form-control" required>
            </div>

            <div class="form-group">
                <label>SMTP port</label>
                <input name="smtp_port" value="465" type="text" placeholder="465" class="form-control" required>
            </div>

            <div class="form-group">
                <label>SMTP username</label>
                <input name="smtp_username" type="text" placeholder="user@mail.com" class="form-control" required>
            </div>

            <div class="form-group">
                <label>SMTP password</label>
                <input name="smtp_password" type="text" placeholder="password" class="form-control" required>
            </div>
        </div>

        <div class="mb-4">
            <h3>Administrator username / password</h3>

            <div class="form-group">
                <label>Admin email</label>
                <input name="admin_email" type="email" placeholder="admin@classified.com" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Admin password</label>
                <input name="admin_password" type="text" placeholder="password" class="form-control" required>
            </div>
        </div>

        <button type="submit" class="btn btn-primary" onclick="return confirm('Is entered configuration correct? Start installation?')" >Install</button>
    </form>

</div>
