<?php

declare(strict_types=1);

namespace App\Service\Cron;

use App\Entity\SystemLog;
use App\Service\System\RunEvery\RunEveryService;
use App\Service\System\SystemLog\SystemLogService;
use App\System\Cache\AppCacheEnum;
use App\System\Lock\AppLockInterface;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Symfony\Component\Lock\Factory;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CronService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var SystemLogService
     */
    private $systemLogService;

    /**
     * @var Factory
     */
    private $lockFactory;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @var RunEveryService
     */
    private $runEveryService;

    public function __construct(
        EntityManagerInterface $em,
        UrlGeneratorInterface $urlGenerator,
        SystemLogService $systemLogService,
        RunEveryService $runEveryService,
        Factory $lockFactory
    ) {
        $this->em = $em;
        $this->systemLogService = $systemLogService;
        $this->lockFactory = $lockFactory;
        $this->urlGenerator = $urlGenerator;
        $this->runEveryService = $runEveryService;
    }

    public function run(): void
    {
        $lock = $this->lockFactory->createLock(AppLockInterface::CRON, 3600);

        if (!$lock->acquire()) {
            return;
        }

        try {
            $this->updateFeatured();
            $this->setMainImage();
            $this->openIndexPage();

            $this->systemLogService->addSystemLog(SystemLog::CRON_RUN_TYPE, "cron executed");
        } finally {
            $lock->release();
        }
    }

    private function updateFeatured(): void
    {
        if (!$this->runEveryService->canRunAgain(AppCacheEnum::CRON_UPDATE_FEATURED, 60*20)) {
            return;
        }

        /** @var \PDO $pdo */
        $pdo = $this->em->getConnection();
        $query = $pdo->prepare(
        /** @lang MySQL */ '
UPDATE listing SET featured=0 WHERE featured_until_date <= :now OR featured_until_date IS NULL
'
        );
        $query->bindValue(':now', date('Y-m-d 00:00:00'));
        $query->execute();
    }

    private function setMainImage(): void
    {
        if (!$this->runEveryService->canRunAgain(AppCacheEnum::CRON_SET_MAIN_IMAGE, 60*25)) {
            return;
        }

        /** @var \PDO $pdo */
        $pdo = $this->em->getConnection();
        $query = $pdo->prepare(
        /** @lang MySQL */ '
UPDATE listing JOIN (
    SELECT path, listing_id FROM listing_file GROUP BY listing_file.id ORDER BY sort ASC
) listing_file ON listing_file.listing_id = listing.id
SET listing.main_image = listing_file.path WHERE 1;
'
        );
        $query->execute();
    }

    private function openIndexPage(): void
    {
        if (!$this->runEveryService->canRunAgain(AppCacheEnum::CRON_OPEN_INDEX, 40)) {
            return;
        }

        $client = new Client([
            RequestOptions::TIMEOUT => 30,
            RequestOptions::CONNECT_TIMEOUT => 30,
            RequestOptions::READ_TIMEOUT => 30,
            RequestOptions::VERIFY => false,
        ]);

        $client->get($this->urlGenerator->generate('app_index', [], UrlGeneratorInterface::ABSOLUTE_URL));
    }
}
