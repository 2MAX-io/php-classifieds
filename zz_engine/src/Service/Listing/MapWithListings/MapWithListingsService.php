<?php

declare(strict_types=1);

namespace App\Service\Listing\MapWithListings;

use App\Entity\Listing;
use App\Enum\ParamEnum;
use App\Service\Listing\ListingPublicDisplayService;
use App\Service\Listing\MapWithListings\Dto\ListingOnMapDto;
use App\Service\Listing\MapWithListings\Dto\MapDefaultConfigDto;
use App\Service\Setting\SettingsService;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class MapWithListingsService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var ListingPublicDisplayService
     */
    private $listingPublicDisplayService;

    /**
     * @var SettingsService
     */
    private $settingsService;

    public function __construct(
        ListingPublicDisplayService $listingPublicDisplayService,
        SettingsService $settingsService,
        EntityManagerInterface $em
    )
    {
        $this->em = $em;
        $this->listingPublicDisplayService = $listingPublicDisplayService;
        $this->settingsService = $settingsService;
    }

    /**
     * @return ListingOnMapDto[]
     */
    public function getListingsOnMap(): iterable
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('listing');
        $qb->from(Listing::class, 'listing');
        $qb->andWhere($qb->expr()->isNotNull('listing.locationLatitude'));
        $qb->andWhere($qb->expr()->isNotNull('listing.locationLongitude'));

        $qb->orderBy('listing.id', Criteria::DESC);
        $qb->setMaxResults(1000);

        $this->listingPublicDisplayService->applyPublicDisplayConditions($qb);

        $results = [];
        foreach ($qb->getQuery()->getResult() as $listing) {
            $results[] = ListingOnMapDto::fromListing($listing);
        }

        return $results;
    }

    public function getDefaultMapConfig(Request $request): MapDefaultConfigDto
    {
        $mapDefaultConfigDto = new MapDefaultConfigDto();
        $mapDefaultConfigDto->setLatitude($this->settingsService->getSettingsDto()->getMapDefaultLatitude());
        $mapDefaultConfigDto->setLongitude($this->settingsService->getSettingsDto()->getMapDefaultLongitude());
        $mapDefaultConfigDto->setZoom($this->settingsService->getSettingsDto()->getMapDefaultZoom());

        $hasLocationInRequest = null !== $request->get(ParamEnum::LATITUDE);
        if ($hasLocationInRequest) {
            $mapDefaultConfigDto->setLatitude((float) $request->get(ParamEnum::LATITUDE));
            $mapDefaultConfigDto->setLongitude((float) $request->get(ParamEnum::LONGITUDE));
            $mapDefaultConfigDto->setZoom((int) $request->get(ParamEnum::ZOOM));
        }

        return $mapDefaultConfigDto;
    }
}
