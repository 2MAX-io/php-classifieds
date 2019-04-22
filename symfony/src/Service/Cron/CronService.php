<?php

declare(strict_types=1);

namespace App\Service\Cron;

use Doctrine\ORM\EntityManagerInterface;

class CronService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function run(): void
    {
        $this->updateFeatured();
        $this->setMainImage();
    }

    private function updateFeatured(): void
    {
        /** @var \PDO $pdo */
        $pdo = $this->em->getConnection();
        $query = $pdo->prepare(/** @lang MySQL */ '
UPDATE listing SET featured=0 WHERE featured_until_date <= :now OR featured_until_date IS NULL
');
        $query->bindValue(':now', date('Y-m-d 00:00:00'));
        $query->execute();
    }

    private function setMainImage(): void
    {
        /** @var \PDO $pdo */
        $pdo = $this->em->getConnection();
        $query = $pdo->prepare(/** @lang MySQL */ '
UPDATE listing JOIN (
    SELECT path, listing_id FROM listing_file GROUP BY listing_file.id ORDER BY sort ASC
) listing_file ON listing_file.listing_id = listing.id
SET listing.main_image = listing_file.path WHERE 1;
');
        $query->execute();
    }
}
