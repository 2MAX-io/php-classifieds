<?php

declare(strict_types=1);

namespace App\Service\Listing\Secondary\PoliceLog;

use App\Entity\Listing;
use App\Entity\Log\PoliceLogListing;
use App\Helper\ServerHelper;
use App\Security\CurrentUserService;
use App\Service\System\SystemLog\PoliceLog\PoliceLogHelperService;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PoliceLogForListingService
{
    /**
     * @var PoliceLogHelperService
     */
    private $policeLogHelperService;

    /**
     * @var CurrentUserService
     */
    private $currentUserService;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    public function __construct(
        PoliceLogHelperService $policeLogHelperService,
        CurrentUserService $currentUserService,
        UrlGeneratorInterface $urlGenerator,
        EntityManagerInterface $em
    ) {
        $this->currentUserService = $currentUserService;
        $this->em = $em;
        $this->policeLogHelperService = $policeLogHelperService;
        $this->urlGenerator = $urlGenerator;
    }

    public function getLogForPolice(Listing $listing): string
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('log');
        $qb->from(PoliceLogListing::class, 'log');
        $qb->andWhere($qb->expr()->orX(
            $qb->expr()->eq('log.listingId', ':listingId'),
            $qb->expr()->andX(
                $qb->expr()->eq('log.userId', ':userId'),
                $qb->expr()->isNull('log.listingId')
            )
        ));
        $qb->setParameter(':listingId', $listing->getId());
        $qb->setParameter(':userId', $listing->getUserNotNull()->getId());

        $qb->addOrderBy('log.datetime', Criteria::ASC);

        /** @var PoliceLogListing[] $logList */
        $logList = $qb->getQuery()->getResult();

        $output = '';
        foreach ($logList as $logListElement) {
            $output .= $logListElement->getText();
            $output .= \str_repeat("\r\n", 1)
                .\str_repeat('=', 100)
                .\str_repeat("\r\n", 3);
        }

        return $output;
    }

    public function saveLog(Listing $listing): void
    {
        $policeLogData = $this->policeLogHelperService->getPoliceLogData();
        $log = new PoliceLogListing();
        $log->setSourceIp($policeLogData->getSourceIp());
        $log->setSourcePort($policeLogData->getSourcePort());
        $log->setDestinationIp($policeLogData->getDestinationIp());
        $log->setDestinationPort($policeLogData->getDestinationPort());
        $log->setDatetime($policeLogData->getDatetime());
        $log->setListingId($listing->getId());

        $listingUrl = $this->urlGenerator->generate(
            'app_listing_show',
            [
                'id' => $listing->getId(),
                'slug' => $listing->getSlug(),
            ],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
        $userEmail = '';
        $user = $this->currentUserService->getUserOrNull();
        if ($user) {
            $log->setUserId($user->getId());
            $userEmail = $user->getEmail();
        }

        $logText = <<<END
{$policeLogData->getConnectionDataText()}

Listing details:
    Title: {$listing->getTitle()}
    Phone: {$listing->getPhone()}
    Price: {$listing->getPrice()}
    City: {$listing->getCity()}
    Email used in listing: {$listing->getEmail()}
    Registered user email: {$userEmail}
    Listing URL: {$listingUrl}
--- 
    Description:
{$listing->getDescription()}
--- Description End


END;

        $logText .= ServerHelper::getServerAsString();
        $log->setText($logText);
        $this->em->persist($log);
    }
}
