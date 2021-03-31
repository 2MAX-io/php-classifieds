<?php

declare(strict_types=1);

namespace App\Tests\Base;

use Symfony\Component\Routing\RouterInterface;

trait RouterHelper
{
    protected function getRouter(): RouterInterface
    {
        /** @var RouterInterface $router */
        $router = static::$kernel->getContainer()->get('router');

        return $router;
    }
}
