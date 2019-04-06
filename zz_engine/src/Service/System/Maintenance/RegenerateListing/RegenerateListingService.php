<?php

declare(strict_types=1);

namespace App\Service\System\Maintenance\RegenerateListing;

use App\Entity\Listing;
use App\Entity\System\ListingInternalData;
use App\Helper\DateHelper;
use App\Service\Listing\Save\SaveListingService;
use App\Service\System\Maintenance\RegenerateListing\Dto\RegenerateListingDto;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Psr\Log\LoggerInterface;

class RegenerateListingService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var SaveListingService
     */
    private $saveListingService;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        SaveListingService $saveListingService,
        EntityManagerInterface $em,
        LoggerInterface $logger
    ) {
        $this->em = $em;
        $this->saveListingService = $saveListingService;
        $this->logger = $logger;
    }

    public function regenerate(RegenerateListingDto $regenerateSearchTextDto): void
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('listing');
        $qb->from(Listing::class, 'listing');

        $qb->addSelect('category0');
        $qb->addSelect('category1');
        $qb->addSelect('category2');
        $qb->addSelect('category3');
        $qb->addSelect('category4');
        $qb->addSelect('category5');
        $qb->addSelect('listingInternalData');
        $qb->addSelect('listingCustomFieldValues');
        $qb->leftJoin('listing.category', 'category0');
        $qb->leftJoin('category0.parent', 'category1');
        $qb->leftJoin('category1.parent', 'category2');
        $qb->leftJoin('category2.parent', 'category3');
        $qb->leftJoin('category3.parent', 'category4');
        $qb->leftJoin('category4.parent', 'category5');

        $qb->leftJoin('listing.listingInternalData', 'listingInternalData');

        $qb->addSelect('listingCustomFieldValues');
        $qb->leftJoin('listing.listingCustomFieldValues', 'listingCustomFieldValues');

        $qb->addSelect('customFieldOption');
        $qb->leftJoin('listingCustomFieldValues.customFieldOption', 'customFieldOption');

        $qb->andWhere($qb->expr()->eq('listing.adminRemoved', 0));
        $qb->andWhere($qb->expr()->eq('listing.userRemoved', 0));
        $qb->andWhere($qb->expr()->gt('listing.expirationDate', ':validAfterDate'));
        $qb->setParameter(':validAfterDate', DateHelper::carbonNow()->subDays(90));

        $qb->orderBy('listingInternalData.lastListingRegenerationDate', Criteria::ASC);
        $qb->groupBy('listing.id');
        $query = $qb->getQuery();

        $stopExecutionTime = null;
        if ($regenerateSearchTextDto->getTimeLimitSeconds()) {
            $stopExecutionTime = DateHelper::timestamp() + $regenerateSearchTextDto->getTimeLimitSeconds();
        }
        $executedCount = 0;
        while ($listings = $this->iterate($query, $executedCount, 50)) {
            foreach ($listings as $listing) {
                $this->logger->debug('processing listing id: {listingId}', [
                    'listingId' => $listing->getId(),
                ]);
                ++$executedCount;
                $this->saveListingService->saveSearchText($listing);
                if ($listing->getListingCustomFieldValues()->count()) {
                    $this->saveListingService->saveCustomFieldsInline($listing);
                }
                $this->em->persist($listing);

                $listingInternalData = $listing->getListingInternalData();
                if (!$listingInternalData) {
                    $listingInternalData = new ListingInternalData();
                    $listingInternalData->setListing($listing);
                }
                $listingInternalData->setLastListingRegenerationDate(DateHelper::create());
                $this->em->persist($listingInternalData);

                if ($stopExecutionTime && DateHelper::timestamp() > $stopExecutionTime) {
                    break 2;
                }
                if ($regenerateSearchTextDto->getLimit() && $executedCount >= $regenerateSearchTextDto->getLimit()) {
                    break 2;
                }
            }

            $this->em->flush();
            $this->em->clear();
            unset($listing);
            \gc_collect_cycles();
        }

        $this->em->flush();
        $this->em->clear();
    }

    /**
     * @return Listing[]
     */
    private function iterate(Query $query, int $offset, int $limit): array
    {
        $query->setFirstResult($offset);
        $query->setMaxResults($limit);

        return $query->getResult();
    }
}
