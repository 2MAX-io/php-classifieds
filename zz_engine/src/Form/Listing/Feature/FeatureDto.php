<?php

declare(strict_types=1);

namespace App\Form\Listing\Feature;

use App\Entity\Package;

class FeatureDto
{
    /**
     * @var Package|null
     */
    private $package;

    public function getPackage(): ?Package
    {
        return $this->package;
    }

    public function setPackage(?Package $package): void
    {
        $this->package = $package;
    }

    public function getPackageNotNull(): Package
    {
        if (null === $this->package) {
            throw new \RuntimeException('package not found');
        }

        return $this->package;
    }
}
