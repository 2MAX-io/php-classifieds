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
        $this->updatePremium();
    }

    private function updatePremium(): void
    {
        /** @var \PDO $pdo */
        $pdo = $this->em->getConnection();
        $query = $pdo->prepare(/** @lang MySQL */ 'UPDATE listing SET premium=0 WHERE premium_until_date <= :now OR premium_until_date IS NULL');
        $query->bindValue(':now', date('Y-m-d 00:00:00'));
        $query->execute();
    }
}
