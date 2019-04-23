<?php

declare(strict_types=1);

namespace App\Service\Cron;

use App\Entity\Listing;
use App\Service\Listing\Save\CreateListingService;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;

class RegenerateSearchTextCron
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var CreateListingService
     */
    private $createListingService;

    public function __construct(EntityManagerInterface $em, CreateListingService $createListingService)
    {
        $this->em = $em;
        $this->createListingService = $createListingService;
    }

    public function regenerate()
    {
        $qb = $this->em->getRepository(Listing::class)->createQueryBuilder('listing');

        $qb->addSelect('category0');
        $qb->addSelect('category1');
        $qb->addSelect('category2');
        $qb->addSelect('category3');
        $qb->addSelect('category4');
        $qb->addSelect('category5');
        $qb->leftJoin('listing.category', 'category0');
        $qb->leftJoin('category0.parent', 'category1');
        $qb->leftJoin('category1.parent', 'category2');
        $qb->leftJoin('category2.parent', 'category3');
        $qb->leftJoin('category3.parent', 'category4');
        $qb->leftJoin('category4.parent', 'category5');

        $qb->addSelect('listingCustomFieldValues');
        $qb->leftJoin('listing.listingCustomFieldValues', 'listingCustomFieldValues');

        $qb->addSelect('customFieldOption');
        $qb->leftJoin('listingCustomFieldValues.customFieldOption', 'customFieldOption');

        $qb->andWhere($qb->expr()->eq('listing.adminRemoved', 0));
        $qb->andWhere($qb->expr()->eq('listing.userRemoved', 0));
        $qb->andWhere($qb->expr()->gt('listing.validUntilDate', ':validAfterDate'));
        $qb->setParameter(':validAfterDate', Carbon::now()->subYears(2));

        $qb->orderBy('listing.lastEditDate', 'DESC');
        $qb->groupBy('listing.id');

        $query = $qb->getQuery();

        $offset = 0;
        while ($listings = $this->iterate($query, $offset, 1000)) {
            foreach ($listings as $listing) {
                $this->createListingService->saveSearchText($listing);
                $this->em->persist($listing);
            }

            $this->em->flush();
            $this->em->clear();

            unset($listing);
        }

        $this->em->flush();
    }

    private function iterate(Query $query, int $offset, int $limit): array
    {
        $query->setFirstResult($offset);
        $query->setMaxResults($limit);

        return $query->getResult();
    }
}
