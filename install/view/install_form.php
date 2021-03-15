<?php

declare(strict_types=1);

include 'base/header.php';
?>

<div class="container">
    <form method="post">
        <div class="float-right mt-2"><a href="https://documentation.2max.io/display/CLAS/Installation" class="btn btn-info" target="_blank">documentation</a></div>
        <h1>Installation</h1>

        <?php foreach ($errors as $error) { ?>
            <div class="alert alert-danger text-break">
                <?php echo e($error); ?>
            </div>
        <?php } ?>

        <?php if (\count($errors)) { ?>
            <div>
                <a href="https://documentation.2max.io/display/CLAS/Installation" class="btn btn-info mb-2" target="_blank">to find help with errors check documentation</a>
            </div>
        <?php } ?>

        <div class="mb-4">
            <h3>Database:</h3>

            <div class="form-group">
                <label>DB host</label>
                <input name="db_host" value="<?php echo e($_POST['db_host'] ?? $formDefaultValue['db_host']); ?>" type="text" placeholder="DB host" class="form-control" required>
            </div>

            <div class="form-group">
                <label>DB port</label>
                <input name="db_port" value="<?php echo e($_POST['db_port'] ?? $formDefaultValue['db_port']); ?>" type="text" placeholder="DB port" class="form-control" required>
            </div>

            <div class="form-group">
                <label>DB database name</label>
                <input name="db_name" value="<?php echo e($_POST['db_name'] ?? $formDefaultValue['db_name']); ?>" type="text" placeholder="database_name" class="form-control" required>
            </div>

            <div class="form-group">
                <label>DB username</label>
                <input name="db_user" value="<?php echo e($_POST['db_user'] ?? $formDefaultValue['db_user']); ?>" type="text" placeholder="username" class="form-control" required>
            </div>

            <div class="form-group">
                <label>DB password</label>
                <input name="db_pass" value="<?php echo e($_POST['db_pass'] ?? $formDefaultValue['db_pass']); ?>" type="text" class="form-control">
            </div>
        </div>

        <div class="mb-4">
            <h3>Sending Emails using SMTP</h3>

            <div class="form-group">
                <label>SMTP host</label>
                <input name="smtp_host" value="<?php echo e($_POST['smtp_host'] ?? $formDefaultValue['smtp_host']); ?>" type="text" placeholder="mail.host.com" class="form-control" required>
            </div>

            <div class="form-group">
                <label>SMTP port</label>
                <input name="smtp_port" value="<?php echo e($_POST['smtp_port'] ?? $formDefaultValue['smtp_port']); ?>" type="text" placeholder="465" class="form-control" required>
            </div>

            <div class="form-group">
                <label>SMTP username</label>
                <input name="smtp_username" value="<?php echo e($_POST['smtp_username'] ?? $formDefaultValue['smtp_username']); ?>" type="text" placeholder="user@mail.com" class="form-control" required>
            </div>

            <div class="form-group">
                <label>SMTP password</label>
                <input name="smtp_password" value="<?php echo e($_POST['smtp_password'] ?? $formDefaultValue['smtp_password']); ?>" type="text" class="form-control" required>
            </div>

            <div class="alert alert-info">
                <div class="mb-2">
                    For email sending to work, make sure that setting bellow ("Email from address") matches email address of SMTP account you provided above.
                    So that you send emails from correct address, otherwise they would not be delivered.
                    After installation, please check if emails are being delivered correctly, you can do that by registering normal user account.
                </div>

                <div>
                    <u>If your "SMTP username" is an email, it should be same as "Email from address"</u>
                </div>
            </div>

            <div class="form-group">
                <label>Email from address</label>
                <input name="email_from_address" value="<?php echo e($_POST['email_from_address'] ?? $formDefaultValue['email_from_address']); ?>" type="email" placeholder="user@mail.com" class="form-control" required>
            </div>
        </div>

        <div class="mb-4">
            <h3>Other settings</h3>

            <div class="form-group">
                <label>Timezone</label>
                <select name="app_timezone" type="text" class="form-control">
                    <?php foreach (\DateTimeZone::listIdentifiers() as $timezone) { ?>
                        <option <?php echo e($timezone === ($_POST['app_timezone'] ?? $formDefaultValue['app_timezone']) ? 'selected' : ''); ?> value="<?php echo e($timezone); ?>"><?php echo e($timezone); ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>

        <div class="mb-4">
            <h3>Administrator username / password</h3>

            <div class="form-group">
                <label>Admin email</label>
                <input name="admin_email" value="<?php echo e($_POST['admin_email'] ?? $formDefaultValue['admin_email']); ?>" type="email" placeholder="admin@classified.com" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Admin password</label>
                <input name="admin_password" value="<?php echo e($_POST['admin_password'] ?? $formDefaultValue['admin_password']); ?>" type="text" placeholder="password" class="form-control" required>
            </div>
        </div>

        <div class="mb-4">
            <h3>License</h3>

            <div class="form-group">
                <label>Copy content of license file into textarea bellow</label>
                <div class="mb-2">
                    <a href="https://documentation.2max.io/display/CLAS/Get+license+file+for+installation" target="_blank">instruction: how to get license file?</a>
                </div>
                <textarea name="license" class="form-control"><?php echo e($_POST['license'] ?? $formDefaultValue['license']); ?></textarea>
            </div>
        </div>

        <div class="mb-4">
            <h3>Load example data</h3>

            <div>
                <input type="checkbox" name="load_categories" id="load-categories" class="custom-checkbox" value="1" <?php echo e(empty($_POST['load_categories']) ?: 'checked'); ?>>
                <label for="load-categories">Load example categories</label>
            </div>
            <div>
                <input type="checkbox" name="load_custom_fields" id="load-custom-fields" class="custom-checkbox" value="1" <?php echo e(empty($_POST['load_custom_fields']) ?: 'checked'); ?>>
                <label for="load-custom-fields">Load example custom fields</label>
            </div>
            <div>
                <input type="checkbox" name="load_listings" id="load-listings" class="custom-checkbox" value="1" <?php echo e(empty($_POST['load_listings']) ?: 'checked'); ?>>
                <label for="load-listings">Load example listings</label>
            </div>
            <div>
                <input type="checkbox" name="load_pages" id="load-pages" class="custom-checkbox" value="1" <?php echo e(empty($_POST['load_pages']) ?: 'checked'); ?>>
                <label for="load-pages">Load example sub-pages like terms and conditions</label>
            </div>
        </div>

        <button type="submit" class="btn btn-primary" onclick="return confirm('Is entered configuration correct? Start the installation?')" >Install</button>
    </form>
</div>
