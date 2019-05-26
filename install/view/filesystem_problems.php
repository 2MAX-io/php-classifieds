<link rel="stylesheet" type="text/css"  href="bootstrap.css" />

<div class="container">
    <h1>Installation filesystem problems</h1>

    <div class="alert alert-danger">
        <p>Invalid permissions, before install you need to apply correct permissions.</p>
        <p>One way to do that is to use commands from textarea bellow in root path of this app.</p>
        <p>Root path of this app is directory with file: zzzz_2max_io_classified_ads_project_root.txt</p>

    <textarea class="w-100">
find <?php echo $projectRootPath ?> -type d -exec chmod 750 {} \;
find <?php echo $projectRootPath ?> -type f -exec chmod 640 {} \;
    </textarea>
    </div>

    <div>
        <h1>Detailed failures</h1>

        <?php if(!$canWriteToPhpFile): ?>
            <div>
                <h3>Could not write to test php file install/data/test.php</h3>

                <div class="alert alert-info">
                    This app would need to modify some php files. For example: install/data/test.php
                    List of all files with incorrect permissions are in other sections.
                </div>
            </textarea>
            </div>
        <?php endif; ?>

        <?php if($creatingDirFailedList): ?>
            <div>
                <h3>Could not create TEST directories</h3>

                <div class="alert alert-info">
                    This app would require creating directories in some places.
                    For example directory to store images of user listing.
                    Bellow is list of test directories that could not be crated.
                    Permissions should be set in a way that alows dirs to be created in those places by app.
                </div>

                <textarea class="w-100">
                <?php foreach ($creatingDirFailedList as $path): ?>
                    <?php echo $path ?>
                <?php endforeach; ?>
            </textarea>
            </div>
        <?php endif; ?>

        <?php if($readingFileFailedList): ?>
            <div>
                <h3>Could not read from files</h3>

                <div class="alert alert-info">
                    Representative examples of files that can not be read by the app.
                    Read permissions should be applied to all files neart those example files.
                    List of all files with incorrect permissions are in other sections.
                </div>

                <textarea class="w-100">
                <?php foreach ($readingFileFailedList as $path): ?>
                    <?php echo $path ?>
                <?php endforeach; ?>
            </textarea>
            </div>
        <?php endif; ?>

        <?php if($writingFileFailedList): ?>
            <div>
                <h3>Could not read from files</h3>

                <div class="alert alert-info">
                    Representative examples of files that can not be written by the app.
                    Write permissions should be applied to all files near those example files.
                    List of all files with incorrect permissions are in other sections.
                </div>

                <textarea class="w-100">
                <?php foreach ($writingFileFailedList as $path): ?>
                    <?php echo $path ?>
                <?php endforeach; ?>
            </textarea>
            </div>
        <?php endif; ?>

        <?php if($incorrectFilePermissionList): ?>
            <div>
                <h3>Files with incorrect permissions, first 5000 (chmod 640)</h3>

                <div class="alert alert-info">
                    Examples of files without read and write permissions.
                </div>

                <textarea class="w-100">
                    <?php foreach ($incorrectFilePermissionList as $path): ?>
                        <?php echo $path ?>
                    <?php endforeach; ?>
                </textarea>
            </div>
        <?php endif; ?>

        <?php if($incorrectDirPermissionList): ?>
            <div>
                <h3>Dirs with incorrect permissions, first 5000</h3>

                <div class="alert alert-info">
                    Examples of directories without read and write permissions (chmod 750).
                </div>

                <textarea class="w-100">
                    <?php foreach ($incorrectDirPermissionList as $path): ?>
                        <?php echo $path ?>
                    <?php endforeach; ?>
                </textarea>
            </div>
        <?php endif; ?>
    </div>

</div>
