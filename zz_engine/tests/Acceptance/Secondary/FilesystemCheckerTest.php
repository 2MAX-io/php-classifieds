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
        self::assertSame([], FilesystemChecker::incorrectFilePermissionList());
        self::assertSame([], FilesystemChecker::incorrectDirPermissionList());
        self::assertSame([], FilesystemChecker::creatingDirFailedList());
        self::assertSame([], FilesystemChecker::writingFileFailedList());
        self::assertSame([], FilesystemChecker::readingFileFailedList());
    }
}
