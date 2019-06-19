<?php

declare(strict_types=1);

namespace App\Service\System\Asset;

use App\Helper\FilePath;
use Symfony\Component\Asset\VersionStrategy\VersionStrategyInterface;

class AssetVersionStrategyService implements VersionStrategyInterface
{
    /**
     * @var string[]
     */
    private $manifestData;

    /**
     * @var string
     */
    private $version;

    public function __construct(string $version = null)
    {
        $this->version = $version;
    }

    /**
     * @inheritDoc
     */
    public function getVersion($path): string
    {
        if (null === $this->manifestData) {
            $manifestPath = FilePath::getAssetBuildDir() . '/rev-manifest.json';
            if (!\file_exists($manifestPath)) {
                throw new \RuntimeException(\sprintf('Asset manifest file "%s" does not exist.', $manifestPath));
            }

            $this->manifestData = \json_decode(\file_get_contents($manifestPath), true);
            if (0 < \json_last_error()) {
                throw new \RuntimeException(\sprintf('Error parsing JSON from asset manifest file "%s" - %s', $manifestPath, \json_last_error_msg()));
            }
        }

        return $this->manifestData[$path] ?? $this->version;
    }

    /**
     * @inheritDoc
     */
    public function applyVersion($path): string
    {
        $versioned = \sprintf('%s?%s', \ltrim($this->getVersion($path), '/'), $this->version);

        if ($path && \strpos($path, '/') === 0) {
            return '/'.$versioned;
        }

        return $versioned;
    }
}
