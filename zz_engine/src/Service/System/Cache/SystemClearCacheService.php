<?php

declare(strict_types=1);

namespace App\Service\System\Cache;

use App\Helper\ExceptionHelper;
use App\System\EnvironmentService;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\CacheClearer\CacheClearerInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class SystemClearCacheService
{
    /**
     * @var CacheClearerInterface
     */
    private $cacheClearer;

    /**
     * @var EnvironmentService
     */
    private $environmentService;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        CacheClearerInterface $cacheClearer,
        Filesystem $filesystem,
        EventDispatcherInterface $eventDispatcher,
        EnvironmentService $environmentService,
        LoggerInterface $logger
    ) {
        $this->cacheClearer = $cacheClearer;
        $this->environmentService = $environmentService;
        $this->filesystem = $filesystem;
        $this->eventDispatcher = $eventDispatcher;
        $this->logger = $logger;
    }

    public function clearAllCaches(): void
    {
        @opcache_reset();
        $this->cacheClearer->clear($this->environmentService->getCacheDir());

        $environmentService = $this->environmentService;
        $filesystem = $this->filesystem;
        $logger = $this->logger;
        $this->eventDispatcher->addListener(
            KernelEvents::TERMINATE,
            static function () use ($environmentService, $filesystem, $logger): void {
                try {
                    $oldCache = $environmentService->getCacheDir() . date('_Ymd_His');
                    $filesystem->rename($environmentService->getCacheDir(), $oldCache);
                    $filesystem->remove($oldCache);
                } catch (\Throwable $e) {
                    $logger->critical('error while deleting all system cache', ExceptionHelper::flatten($e));
                }
            }
        );
    }
}
