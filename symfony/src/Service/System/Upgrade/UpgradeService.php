<?php

declare(strict_types=1);

namespace App\Service\System\Upgrade;

use App\Exception\UserVisibleMessageException;
use App\Helper\FilePath;
use App\Helper\LoggerException;
use App\Helper\Random;
use App\Service\System\Signature\SignatureVerifyHighSecurity;
use App\System\EnvironmentService;
use Psr\Log\LoggerInterface;

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

    public function __construct(EnvironmentService $environmentService, LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->environmentService = $environmentService;
    }

    public function upgrade(array $upgradeArr): void
    {
        if ($this->environmentService->getUpgradeDisabled()) {
            throw new UserVisibleMessageException('trans.The update option has been manually disabled in configuration. If you plan to enable it, make sure that you have not made any changes to the application code.');
        }

        foreach ($upgradeArr['upgradeList'] as $upgradeItem) {
            $type = $upgradeItem['type'];

            if ($type === 'file') {
                $this->runFileUpgrade($upgradeItem['id'], $upgradeItem['content'], $upgradeItem['contentSignature']);
            }
        }
    }

    public function runFileUpgrade(int $id, string $content, string $signature): void
    {
        if ($this->environmentService->getUpgradeDisabled()) {
            throw new UserVisibleMessageException('trans.The update option has been manually disabled in configuration. If you plan to enable it, make sure that you have not made any changes to the application code.');
        }

        if (!SignatureVerifyHighSecurity::authenticate($content, $signature)) {
            return;
        }

        if (!\file_exists(FilePath::getUpgradeDir())) {
            \mkdir(FilePath::getUpgradeDir(), 0775);
        }

        $decodedContent = \base64_decode($content);
        $filename = \basename(
            'upgrade'
            . '_' . (int) $id
            . '_' . \md5($decodedContent)
            . '_' . date('Y_m_d__His')
            . '_' . Random::string(32)
            . '.php'
        );
        $path = FilePath::getUpgradeDir() . '/' . $filename;
        \file_put_contents($path, $decodedContent);

        try {
            $run = include $path;
            $run();
        } catch (\Throwable $e) {
            $this->logger->alert('error during upgrade', LoggerException::flatten($e));
            $this->logger->alert('error during upgrade, content', [
                'run_content' => $content,
            ]);
            throw $e;
        } finally {
//            \unlink($path);
        }
    }
}
