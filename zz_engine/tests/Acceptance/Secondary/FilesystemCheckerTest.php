<?php

declare(strict_types=1);

namespace App\Tests\Acceptance\Secondary;

use App\Secondary\Filesystem\FilesystemChecker;
use App\Tests\Base\AppIntegrationTestCase;

/**
 * @internal
 */
class FilesystemCheckerTest extends AppIntegrationTestCase
{
    public function test(): void
    {
        self::assertCount(0, FilesystemChecker::incorrectFilePermissionList(), \print_r(FilesystemChecker::incorrectFilePermissionList(), true));
        self::assertCount(0, FilesystemChecker::incorrectDirPermissionList());
        self::assertCount(0, FilesystemChecker::creatingDirFailedList());
        self::assertCount(0, FilesystemChecker::writingFileFailedList());
        self::assertCount(0, FilesystemChecker::readingFileFailedList());
    }
}
