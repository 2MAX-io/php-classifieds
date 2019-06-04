<title>Install</title>

<link rel="stylesheet" type="text/css"  href="bootstrap.css" />

<div class="container">

    <form action="" method="post">
        <h1>Installation</h1>

        <?php foreach ($errors as $error): ?>
            <div class="alert alert-danger">
                <?php echo escape($error) ?>
            </div>
        <?php endforeach; ?>

        <div class="mb-4">
            <h3>Database:</h3>

            <div class="form-group">
                <label>DB host</label>
                <input name="db_host" value="<?php echo escape($_POST['db_host'] ?? 'localhost') ?>" type="text" placeholder="DB host" class="form-control" required>
            </div>

            <div class="form-group">
                <label>DB port</label>
                <input name="db_port" value="<?php echo escape($_POST['db_port'] ?? '3306') ?>" type="text" placeholder="DB port" class="form-control" required>
            </div>

            <div class="form-group">
                <label>DB database name</label>
                <input name="db_name" value="<?php echo escape($_POST['db_name'] ?? '') ?>" type="text" placeholder="database_name" class="form-control" required>
            </div>

            <div class="form-group">
                <label>DB username</label>
                <input name="db_user" value="<?php echo escape($_POST['db_user'] ?? '') ?>" type="text" placeholder="username" class="form-control" required>
            </div>

            <div class="form-group">
                <label>DB password</label>
                <input name="db_pass" value="<?php echo escape($_POST['db_pass'] ?? '') ?>" type="text" class="form-control">
            </div>
        </div>

        <div class="mb-4">
            <h3>Sending Emails using SMTP</h3>

            <div class="form-group">
                <label>SMTP host</label>
                <input name="smtp_host" value="<?php echo escape($_POST['smtp_host'] ?? '') ?>" type="text" placeholder="mail.host.com" class="form-control" required>
            </div>

            <div class="form-group">
                <label>SMTP port</label>
                <input name="smtp_port" value="<?php echo escape($_POST['smtp_port'] ?? '465') ?>" type="text" placeholder="465" class="form-control" required>
            </div>

            <div class="form-group">
                <label>SMTP username</label>
                <input name="smtp_username" value="<?php echo escape($_POST['smtp_username'] ?? '') ?>" type="text" placeholder="user@mail.com" class="form-control" required>
            </div>

            <div class="form-group">
                <label>SMTP password</label>
                <input name="smtp_password" value="<?php echo escape($_POST['smtp_password'] ?? '') ?>" type="text" class="form-control" required>
            </div>

            <div class="alert alert-info">
                <div class="mb-2">
                    For email sending to work, make sure that setting bellow ("Email from address") matches email address of SMTP account you provided above.
                    So that you send emails from correct address, otherwise they would not be delivered.
                    After installation, please check if emails are being delivered correctly, you can do that by registering normal user account.
                </div>

                <div>
                    <u>If your SMTP username is an email, it should be same as "SMTP username"</u>
                </div>
            </div>

            <div class="form-group">
                <label>Email from address</label>
                <input name="email_from_address" value="<?php echo escape($_POST['email_from_address'] ?? '') ?>" type="email" placeholder="user@mail.com" class="form-control" required>
            </div>
        </div>

        <div class="mb-4">
            <h3>Other settings</h3>

            <div class="form-group">
                <label>Timezone</label>
                <select name="app_timezone" type="text" class="form-control">
                    <?php foreach (\DateTimeZone::listIdentifiers() as $timezone): ?>
                        <option <?php echo $timezone === ($_POST['app_timezone'] ?? 'UTC') ? 'selected' : '' ?> value="<?php echo $timezone ?>"><?php echo $timezone ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="mb-4">
            <h3>Administrator username / password</h3>

            <div class="form-group">
                <label>Admin email</label>
                <input name="admin_email" value="<?php echo escape($_POST['admin_email'] ?? '') ?>" type="email" placeholder="admin@classified.com" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Admin password</label>
                <input name="admin_password" value="<?php echo escape($_POST['admin_password'] ?? '') ?>" type="text" placeholder="password" class="form-control" required>
            </div>
        </div>

        <div class="mb-4">
            <div>
                <input type="checkbox" name="load_categories" id="load-categories" class="custom-checkbox" value="1">
                <label for="load-categories">Load example categories</label>
            </div>
            <div>
                <input type="checkbox" name="load_custom_fields" id="load-custom-fields" class="custom-checkbox" value="1">
                <label for="load-custom-fields">Load example custom fields</label>
            </div>
            <div>
                <input type="checkbox" name="load_listings" id="load-listings" class="custom-checkbox" value="1">
                <label for="load-listings">Load example listings</label>
            </div>
        </div>

        <button type="submit" class="btn btn-primary" onclick="return confirm('Is entered configuration correct? Start the installation?')" >Install</button>
    </form>

</div>
