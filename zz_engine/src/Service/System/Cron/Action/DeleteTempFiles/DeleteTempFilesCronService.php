<?php

declare(strict_types=1);

namespace App\Service\System\Cron\Action\DeleteTempFiles;

use App\Helper\DateHelper;
use App\Helper\FilePath;
use App\Helper\StringHelper;
use App\Service\System\Cron\Action\Base\CronActionInterface;
use App\Service\System\Cron\Action\Base\CronAtNightInterface;
use App\Service\System\Cron\Dto\CronDto;
use App\Service\System\RunEvery\RunEveryService;
use Carbon\Carbon;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class DeleteTempFilesCronService implements CronActionInterface, CronAtNightInterface, MessageHandlerInterface
{
    /**
     * @var LockFactory
     */
    private $lockFactory;

    /**
     * @var MessageBusInterface
     */
    private $messageBus;

    /**
     * @var RunEveryService
     */
    private $runEveryService;

    public function __construct(
        RunEveryService $runEveryService,
        LockFactory $lockFactory,
        MessageBusInterface $messageBus
    ) {
        $this->lockFactory = $lockFactory;
        $this->messageBus = $messageBus;
        $this->runEveryService = $runEveryService;
    }

    public function __invoke(DeleteTempFilesMessage $deleteExpiredListingFilesMessage): void
    {
        $lockName = 'lock_deleteTempFilesLock';
        $lock = $this->lockFactory->createLock($lockName, 3600);
        if (!$lock->acquire()) {
            return;
        }

        try {
            $this->deleteTempUploadFiles();
            $this->deleteImageResizeCache();
        } finally {
            $lock->release();
        }
    }

    public function runFromCron(CronDto $cronDto): void
    {
        if (!$cronDto->getIgnoreDelay() && !$this->runEveryService->canRunAgain('cron_deleteTempFiles', 16 * 3600)) {
            return;
        }

        $this->messageBus->dispatch(new DeleteTempFilesMessage());
    }

    private function deleteTempUploadFiles(): void
    {
        $tempFileUploadPath = FilePath::getTempFileUpload();
        $localAdapter = new Local($tempFileUploadPath);
        $filesystem = new Filesystem($localAdapter);
        $finder = new Finder();
        $finder->in($tempFileUploadPath);
        $finder->depth(3);
        $finder->ignoreDotFiles(true);
        $finder->ignoreVCS(true);

        foreach ($finder->files()->getIterator() as $file) {
            if (!StringHelper::match('~^\d\d\d\d/\d\d/\d\d$~', $file->getRelativePath())) {
                continue;
            }

            $date = Carbon::createFromFormat('Y/m/d', $file->getRelativePath());
            if ($date && \abs($date->diff(DateHelper::carbonNow())->days ?: 0) > 3) {
                $filesystem->deleteDir($file->getRelativePath());
            }
        }

        $finder = new Finder();
        $finder->in($tempFileUploadPath);
        $finder->directories();
        $finder->ignoreDotFiles(true);
        $finder->ignoreVCS(true);
        $finder->depth('<= 3');
        $finder->sort(static function (\SplFileInfo $a, \SplFileInfo $b) {
            if (false === $a->getRealPath() || false === $b->getRealPath()) {
                return 0;
            }

            $depth = \substr_count($a->getRealPath(), '/') - \substr_count($b->getRealPath(), '/');

            return (0 === $depth) ? \strcmp($a->getRealPath(), $b->getRealPath()) : $depth;
        });
        $finder->reverseSorting();
        foreach ($finder->directories()->getIterator() as $file) {
            if (false === $file->getRealPath()) {
                continue;
            }
            $isEmpty = !(new \FilesystemIterator($file->getRealPath()))->valid();
            if ($isEmpty) {
                $filesystem->deleteDir($file->getRelativePathname());
            }
        }
    }

    private function deleteImageResizeCache(): void
    {
        $cachePath = FilePath::getStaticPath().'/cache';

        $localAdapter = new Local($cachePath);
        $filesystem = new Filesystem($localAdapter);

        $finder = new Finder();
        $finder->in($cachePath);
        $finder->directories();
        $finder->ignoreDotFiles(true);
        $finder->ignoreVCS(true);
        $finder->sort(static function (\SplFileInfo $a, \SplFileInfo $b) {
            if (false === $a->getRealPath() || false === $b->getRealPath()) {
                return 0;
            }

            $depth = \substr_count($a->getRealPath(), '/') - \substr_count($b->getRealPath(), '/');

            return (0 === $depth) ? \strcmp($a->getRealPath(), $b->getRealPath()) : $depth;
        });
        $finder->reverseSorting();
        foreach ($finder->directories()->getIterator() as $file) {
            if (false === $file->getRealPath()) {
                continue;
            }
            $filesystem->deleteDir($file->getRelativePathname());
        }
    }
}
