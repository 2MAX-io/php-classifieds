<?php

declare(strict_types=1);

namespace App\Service\System\Upgrade;

use App\Helper\FilePath;
use App\Helper\LoggerException;
use App\Helper\Random;
use App\Helper\Str;
use Psr\Log\LoggerInterface;

class UpgradeService
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function upgrade(array $upgradeArr): void
    {
        foreach ($upgradeArr['upgradeList'] as $upgradeItem) {
            $type = $upgradeItem['type'];

            if ($type === 'file') {
                $this->runTypeFile($upgradeItem['id'], $upgradeItem['content'], $upgradeItem['contentSignature']);
            }
        }
    }

    public function runTypeFile(int $id, string $content, string $signature): void
    {
        if (!VerifyHighSecurity::authenticate($content, $signature)) {
            return;
        }

        if (!\file_exists(FilePath::getUpgradeDir())) {
            \mkdir(FilePath::getUpgradeDir(), 0775);
        }

        $decodedContent = \base64_decode($content);
        $path = FilePath::getUpgradeDir() . '/' . \basename('upgrade_' . (int) $id . '_' . \md5($decodedContent) . '_' . date('Y_m_d__His') . '_' . Random::string(32) . '.php');
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
