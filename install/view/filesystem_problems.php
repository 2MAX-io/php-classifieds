<?php

declare(strict_types=1);

$pageTitle = 'Install - problems';

include 'base/header.php';
?>

<div class="container">
    <div class="float-right mt-2"><a href="https://documentation.2max.io/display/CLAS/Required+permissions" class="btn btn-info" target="_blank">documentation</a></div>
    <h1>Installation - 2max.io Classified Ads</h1>
    <h2>Filesystem problems</h2>

    <div class="float-right"><a href="https://documentation.2max.io/display/CLAS/Requirements" class="btn btn-info">documentation</a></div>

    <div class="alert alert-danger">
        <h4 class="text-danger">Invalid permissions, before installation begins, you need to apply correct permissions.</h4>
        <p>One way to do that, is to use commands from textarea bellow in <a href="https://documentation.2max.io/display/CLAS/Where+is+app+root+directory" target="_blank">root path of this app</a>.</p>
        <p>Root path of this app is directory with file: zzzz_2max_io_classified_ads_project_root.txt</p>

        <textarea class="w-100 form-control chmod-commands">
find <?php echo e($projectRootPath); ?> -type d -exec chmod 750 {} \;
find <?php echo e($projectRootPath); ?> -type f -exec chmod 640 {} \;
chmod +x <?php echo e($projectRootPath); ?>/zz_engine/bin/console;
        </textarea>

        <p>If above commands would not work on it's own. Other potential issue may be incorrect <b>chown</b> settings.</p>
        <p>All project files should be readable and writable both by your current server's user and by webserver process (most common webserver users are www-data, www, apache, nobody or your current server user in shared environments)</p>
        <p>In most cases running one of commands bellow would fix problems, but do not run them if you do not know what exactly are you doing</p>

        <textarea class="w-100 form-control chown-commands">
chown -R YOUR_CURRENT_USER_HERE:YOUR_CURRENT_USER_HERE <?php echo e($projectRootPath); ?>;
chown -R YOUR_CURRENT_USER_HERE:www-data <?php echo e($projectRootPath); ?>;
chown -R www-data:www-data <?php echo e($projectRootPath); ?>;
        </textarea>

        <p>in commands above change <u>www-data</u> to group of your webserver process, if it is different</p>
        
        <p>More info about setting up correct permissions can be found in <a href="https://documentation.2max.io/display/CLAS/Required+permissions" target="_blank">documentation</a></p>
    </div>

    <div class="mb-2">
        <a href="" class="btn btn-primary">Refresh</a>
    </div>

    <div class="mb-5">
        <h1>Detailed list of problems</h1>

        <div class="alert alert-warning">
            Commands above should fix permissions issues, if not, bellow is detailed list of problems that should help troubleshoot issues.
        </div>

        <?php if (!$canWriteToPhpFile) { ?>
            <div class="mb-3">
                <h3>Could not write to test php file install/data/test.php</h3>

                <div class="alert alert-info">
                    <div>This app would need to modify some php files. For example: install/data/test.php</div>
                    <div>List of all files with incorrect permissions can be found in other sections.</div>
                </div>
            </div>
        <?php } ?>

        <?php if (!$canExecuteConsole) { ?>
            <div class="mb-3">
                <h3>Could not execute zz_engine/bin/console, add execution permissions for this file using chmod +x</h3>

                <div class="alert alert-info">
                    <div>required so that cron could be executed</div>
                </div>
            </div>
        <?php } ?>

        <?php if ($creatingDirFailedList) { ?>
            <div class="mb-3">
                <h3>Could not create test directories</h3>

                <div class="alert alert-info">
                    <div>
                        This app requires creating directories in several locations.
                        For example, directory to store user's listing images.
                    </div>
                    <div>Bellow is list of test directories that could not be crated.</div>
                    <div>Permissions should be set in a way, that allows dirs to be created in those locations by the app.</div>
                </div>

                <textarea class="w-100 h-10em form-control">
<?php echo e(\implode("\n", $creatingDirFailedList)); ?>
		        </textarea>
            </div>
        <?php } ?>

        <?php if ($readingFileFailedList) { ?>
            <div class="mb-3">
                <h3>Could not read from files</h3>

                <div class="alert alert-info">
                    <div>Representative examples of files that can not be read by the app.</div>
                    <div>Read permissions should be applied to all files near those example files.</div>
                    <div>List of all files with incorrect permissions can be found in other sections.</div>
                </div>

                <textarea class="w-100 h-10em form-control">
<?php echo e(\implode("\n", $readingFileFailedList)); ?>
                </textarea>
            </div>
        <?php } ?>

        <?php if ($writingFileFailedList) { ?>
            <div class="mb-3">
                <h3>Could not write to files in directories</h3>

                <div class="alert alert-info">
                    <div>Representative examples of directories to which file could not be written by the app.</div>
                    <div>Write permissions should be applied to those directories and files within them.</div>
                    <div>List of all files with incorrect permissions can be found in other sections.</div>
                </div>

                <textarea class="w-100 h-10em form-control">
<?php echo e(\implode("\n", $writingFileFailedList)); ?>
                </textarea>
            </div>
        <?php } ?>

        <?php if ($incorrectFilePermissionList) { ?>
            <div class="mb-3">
                <h3>Files with incorrect permissions, first 5000</h3>

                <div class="alert alert-info">
                    Examples of files without read and write permissions (chmod 640).
                </div>

                <textarea class="w-100 h-10em form-control">
<?php echo e(\implode("\n", $incorrectFilePermissionList)); ?>
                </textarea>
            </div>
        <?php } ?>

        <?php if ($incorrectDirPermissionList) { ?>
            <div class="mb-3">
                <h3>Dirs with incorrect permissions, first 5000</h3>

                <div class="alert alert-info">
                    Examples of directories without read and write permissions (chmod 750).
                </div>

                <textarea class="w-100 h-10em form-control">
<?php echo e(\implode("\n", $incorrectDirPermissionList)); ?>
                </textarea>
            </div>
        <?php } ?>
    </div>
</div>
