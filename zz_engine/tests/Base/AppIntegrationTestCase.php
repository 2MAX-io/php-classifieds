<?php

declare(strict_types=1);

namespace App\Tests\Base;

use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @internal
 * @coversNothing
 */
class AppIntegrationTestCase extends WebTestCase
{
    protected static function createClient(array $options = [], array $server = []): KernelBrowser
    {
        $return = parent::createClient($options, $server);
        $GLOBALS['kernel'] = static::$kernel;

        return $return;
    }

    public function getTestContainer(): ContainerInterface
    {
        return self::$container;
    }
}
