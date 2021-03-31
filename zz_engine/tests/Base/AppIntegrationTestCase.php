<?php

declare(strict_types=1);

namespace App\Tests\Base;

use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @internal
 * @coversNothing
 */
class AppIntegrationTestCase extends WebTestCase
{
    public function getTestContainer(): ContainerInterface
    {
        return self::$container;
    }
}
