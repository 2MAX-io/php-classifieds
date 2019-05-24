<?php

declare(strict_types=1);

namespace App\Service\System\SystemLog;

use App\Entity\SystemLog;
use Doctrine\ORM\EntityManagerInterface;

class SystemLogService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function addSystemLog(string $type, string $message): void
    {
        $systemLog = new SystemLog();
        $systemLog->setDate(new \DateTime());
        $systemLog->setType($type);
        $systemLog->setMessage($message);
        $this->em->persist($systemLog);
    }
}
