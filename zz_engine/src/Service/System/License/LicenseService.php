<?php

declare(strict_types=1);

namespace App\Service\System\License;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class LicenseService
{
    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function getCurrentUrlOfLicense(): string
    {
        return $this->urlGenerator->generate('app_index', [], UrlGeneratorInterface::ABSOLUTE_URL);
    }
}
