<?php

declare(strict_types=1);

namespace App\Service\System\SystemLog;

use App\Entity\System\SystemLog;
use App\Helper\DateHelper;
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
        $systemLog->setDate(DateHelper::create());
        $systemLog->setType($type);
        $systemLog->setMessage($message);
        $this->em->persist($systemLog);
    }
}
