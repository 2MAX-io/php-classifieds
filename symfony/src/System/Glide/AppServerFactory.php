<?php

declare(strict_types=1);

namespace App\System\Glide;

use League\Glide\Manipulators\Orientation;
use League\Glide\Server;
use League\Glide\ServerFactory;

/**
 * same as \League\Glide\ServerFactory
 * except removing Orientation because it requires EXIF
 * https://github.com/thephpleague/glide/issues/176#issuecomment-279174123
 */
class AppServerFactory extends ServerFactory
{
    /**
     * @inheritDoc
     */
    public function getManipulators()
    {
        return array_filter(
            parent::getManipulators(),
            function ($element) {
                if ($element instanceof Orientation) {
                    return false; // removing orientation because it uses EXIF
                }

                return true;
            }
        );
    }

    /**
     * Create configured server.
     * @param  array  $config Configuration parameters.
     * @return Server Configured server.
     */
    public static function create(array $config = [])
    {
        return (new self($config))->getServer();
    }
}
