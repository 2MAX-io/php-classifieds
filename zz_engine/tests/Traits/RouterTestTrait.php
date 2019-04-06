<?php

declare(strict_types=1);

namespace App\Tests\Traits;

use Symfony\Component\Routing\RouterInterface;

trait RouterTestTrait
{
    protected function getRouter(): RouterInterface
    {
        /** @var RouterInterface $router */
        $router = static::$kernel->getContainer()->get('router');

        return $router;
    }
}
