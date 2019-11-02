<?php

declare(strict_types=1);

namespace App\Service\System\Upgrade;

use App\Exception\UserVisibleException;
use App\Helper\FilePath;
use App\Helper\ExceptionHelper;
use App\Helper\Random;
use App\Service\System\Cache\SystemClearCacheService;
use App\Service\System\Signature\SignatureVerifyHighSecurity;
use App\System\EnvironmentService;
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;

class UpgradeService
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var EnvironmentService
     */
    private $environmentService;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var SystemClearCacheService
     */
    private $systemClearCacheService;

    public function __construct(EnvironmentService $environmentService, Filesystem $filesystem, SystemClearCacheService $systemClearCacheService, LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->environmentService = $environmentService;
        $this->filesystem = $filesystem;
        $this->systemClearCacheService = $systemClearCacheService;
    }

    public function upgrade(array $upgradeArr): void
    {
        if ($this->environmentService->getUpgradeDisabled()) {
            throw new UserVisibleException('trans.The update option has been manually disabled in configuration. If you plan to enable it, make sure that you have not made any changes to the application code.');
        }

        foreach ($upgradeArr['upgradeList'] as $upgradeItem) {
            $type = $upgradeItem['type'];

            if ($type === 'file') {
                $this->runFileUpgrade($upgradeItem['id'], $upgradeItem['content'], $upgradeItem['contentSignature']);
            }
        }

        $this->systemClearCacheService->clearAllCaches();
    }

    public function runFileUpgrade(int $id, string $content, string $signature): void
    {
        if ($this->environmentService->getUpgradeDisabled()) {
            throw new UserVisibleException('trans.The update option has been manually disabled in configuration. If you plan to enable it, make sure that you have not made any changes to the application code.');
        }

        if (!SignatureVerifyHighSecurity::authenticate($content, $signature)) {
            $this->logger->error('could not verify high security signature during upgrade');

            return;
        }

        if (!\file_exists(FilePath::getUpgradeDir())) {
            $this->filesystem->mkdir(FilePath::getUpgradeDir(), 0770);
        }

        $decodedContent = \base64_decode($content);
        $filename = \basename(
            'upgrade'
            . '_' . $id
            . '_' . \md5($decodedContent)
            . '_' . \date('Y_m_d__His')
            . '_' . Random::string(8)
            . '.php'
        );
        $path = FilePath::getUpgradeDir() . '/' . $filename;
        \file_put_contents($path, $decodedContent);

        try {
            /** @noinspection PhpIncludeInspection */
            $run = include $path;
            $run();
        } catch (\Throwable $e) {
            $this->logger->alert(
                'error during upgrade, content',
                [
                    'run_content' => $content,
                ]
            );
            $this->logger->alert('error during upgrade', ExceptionHelper::flatten($e));
            throw $e;
        }
    }
}
