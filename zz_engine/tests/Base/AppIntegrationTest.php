<?php

declare(strict_types=1);

namespace App\Tests\Base;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AppIntegrationTest extends WebTestCase
{
    public function getTestContainer(): \Symfony\Bundle\FrameworkBundle\Test\TestContainer
    {
        /* @noinspection PhpIncompatibleReturnTypeInspection */
        return self::$container;
    }
}
