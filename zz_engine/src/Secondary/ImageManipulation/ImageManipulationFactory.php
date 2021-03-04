<?php

declare(strict_types=1);

namespace App\Secondary\ImageManipulation;

use League\Glide\Manipulators\ManipulatorInterface;
use League\Glide\Manipulators\Orientation;
use League\Glide\Server;
use League\Glide\ServerFactory;

/**
 * same as \League\Glide\ServerFactory
 * except removing Orientation because it requires EXIF
 * https://github.com/thephpleague/glide/issues/176#issuecomment-279174123
 */
class ImageManipulationFactory extends ServerFactory
{
    /**
     * @param array<array-key, mixed> $config
     */
    public static function create(array $config = []): Server
    {
        return (new self($config))->getServer();
    }

    /**
     * @return ManipulatorInterface[]
     */
    public function getManipulators(): array
    {
        return \array_filter(
            parent::getManipulators(),
            static function ($element) {
                return !$element instanceof Orientation; // removing orientation because it uses EXIF
            }
        );
    }
}
