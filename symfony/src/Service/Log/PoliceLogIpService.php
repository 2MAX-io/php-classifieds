<?php

declare(strict_types=1);

namespace App\Service\Log;

use App\Entity\Listing;
use App\Entity\ListingLog;
use App\Security\CurrentUserService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class PoliceLogIpService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var CurrentUserService
     */
    private $currentUserService;

    public function __construct(EntityManagerInterface $em, CurrentUserService $currentUserService)
    {
        $this->em = $em;
        $this->currentUserService = $currentUserService;
    }

    public function prepareOutput(Listing $listing): string
    {
        $qb = $this->em->getRepository(ListingLog::class)->createQueryBuilder('log');
        $qb->andWhere($qb->expr()->orX(
            $qb->expr()->eq('log.listingId', ':listingId'),
            $qb->expr()->andX(
                $qb->expr()->eq('log.userId', ':userId'),
                $qb->expr()->isNull('log.listingId')
            )
        ));
        $qb->setParameter(':listingId', $listing->getId());
        $qb->setParameter(':userId', $listing->getUser()->getId());

        $qb->addOrderBy('log.datetime', 'ASC');

        /** @var ListingLog[] $logList */
        $logList = $qb->getQuery()->getResult();

        $output = '';
        foreach ($logList as $logListElement) {
            $output .= $logListElement->getText();
            $output .= str_repeat("\r\n", 1) . str_repeat('=', 100) . str_repeat("\r\n", 3);
        }

        return $output;
    }

    public function saveLog(Listing $listing)
    {
        $log = new ListingLog();
        $log->setSourceIp($_SERVER['REMOTE_ADDR']);
        $log->setDestinationIp($_SERVER['SERVER_ADDR']);
        $log->setDatetime(\DateTime::createFromFormat('U', (string) $_SERVER['REQUEST_TIME']));
        $log->setListingId($listing->getId());

        $userEmail = '';
        $user = $this->currentUserService->getUser();
        if ($user) {
            $log->setUserId($user->getId());
            $userEmail = $user->getEmail();
        }

        $requestTimeString = DateTime::createFromFormat('U.u', (string) $_SERVER['REQUEST_TIME_FLOAT'])->format('Y-m-d H:i:s.u P');
        $currentServerTime = DateTime::createFromFormat('U.u', (string) microtime(true))->format('Y-m-d H:i:s.u P');

        $logText = <<<END
Connection:
{$_SERVER['REMOTE_ADDR']}:{$_SERVER['REMOTE_PORT']} --> {$_SERVER['SERVER_ADDR']}:{$_SERVER['SERVER_PORT']}

Request time: {$requestTimeString}
Unix request time float: {$_SERVER['REQUEST_TIME_FLOAT']}
Server time when saving this log: {$currentServerTime}

Listing details:
    Title: {$listing->getTitle()}
    Description: {$listing->getDescription()}
    Phone: {$listing->getPhone()}
    Price: {$listing->getPrice()}
    City: {$listing->getCity()}
    Email used in listing: {$listing->getEmail()}
    Registered user email: {$userEmail}


END;

        $logText .= $this->getServerAsString();

        $log->setText($logText);

        $this->em->persist($log);
    }

    private function getServerAsString(): string
    {
        $otherInfo = [
            'REQUEST_URI' => $_SERVER['REQUEST_URI'] ?? '',
            'REQUEST_METHOD' => $_SERVER['REQUEST_METHOD'] ?? '',
            'HTTP_USER_AGENT' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            'SERVER_ADDR' => $_SERVER['SERVER_ADDR'],
            'SERVER_PORT' => $_SERVER['SERVER_PORT'],
            'REMOTE_ADDR' => $_SERVER['REMOTE_ADDR'],
            'REMOTE_PORT' => $_SERVER['REMOTE_PORT'],
            'REMOTE_HOST' => $_SERVER['REMOTE_HOST'] ?? '',
            'HTTP_CF_CONNECTING_IP' => $_SERVER['HTTP_CF_CONNECTING_IP'] ?? '',
            'HTTP_ORIGIN' => $_SERVER['HTTP_ORIGIN'] ?? '',
            'SERVER_NAME' => $_SERVER['SERVER_NAME'] ?? '',
            'HTTP_HOST' => $_SERVER['HTTP_HOST'] ?? '',
            'SERVER_PROTOCOL' => $_SERVER['SERVER_PROTOCOL'] ?? '',
            'HTTP_REFERER' => $_SERVER['HTTP_REFERER'] ?? '',
            'REQUEST_TIME_FLOAT' => $_SERVER['REQUEST_TIME_FLOAT'],
        ];

        $return = '';
        foreach ($otherInfo as $name => $value) {
            $return .= "$name => {$value} \r\n";
        }

        return $return;
    }
}
