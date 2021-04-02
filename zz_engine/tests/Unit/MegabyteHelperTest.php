<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Helper\MegabyteHelper;
use App\Tests\Base\AppTestCase;

/**
 * @internal
 */
class MegabyteHelperTest extends AppTestCase
{
    public function testConversion(): void
    {
        self::assertSame(10485760, MegabyteHelper::toByes(10));
    }
}
