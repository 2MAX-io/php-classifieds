<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Helper\BoolHelper;
use App\Tests\Base\AppTestCase;

/**
 * @internal
 */
class BoolHelperTest extends AppTestCase
{
    /**
     * @dataProvider dataProvider
     *
     * @param string|int|null $value
     */
    public function test($value, bool $expected): void
    {
        self::assertSame($expected, BoolHelper::isTrue($value));
    }

    /**
     * @dataProvider dataProviderWithCast
     *
     * @param string|int|null $value
     */
    public function testWithCast($value, bool $expected): void
    {
        self::assertSame($expected, BoolHelper::isTrue($value, true), (string) $value);
    }

    /**
     * @return array[]
     */
    public function dataProvider(): array
    {
        return [
            [true, true],
            ['true', true],
            [1, true],
            ['1', true],

            [false, false],
            [null, false],
            ['false', false],
            [0, false],
            ['0', false],
            ['', false],

            // no match found, and cast disable
            ['text', false],
        ];
    }

    /**
     * @return array[]
     */
    public function dataProviderWithCast(): array
    {
        return [
            ['11111', true],
            [1111, true],

            [000, false],
            ['0000', true],
            ['text', true],
        ];
    }
}
