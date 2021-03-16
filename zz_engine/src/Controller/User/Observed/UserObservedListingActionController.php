<?php

declare(strict_types=1);

namespace App\Controller\User\Observed;

use App\Controller\User\Base\AbstractUserController;
use App\Entity\Listing;
use App\Entity\UserObservedListing;
use App\Enum\ParamEnum;
use App\Helper\BoolHelper;
use App\Helper\DateHelper;
use App\Security\CurrentUserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserObservedListingActionController extends AbstractUserController
{
    /**
     * @var CurrentUserService
     */
    private $currentUserService;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(CurrentUserService $currentUserService, EntityManagerInterface $em)
    {
        $this->currentUserService = $currentUserService;
        $this->em = $em;
    }

    /**
     * @Route("/user/listing/observed", name="app_user_observed_listing_set", options={"expose": true})
     */
    public function markUserObservedListing(Request $request): Response
    {
        $this->dennyUnlessUser();

        $listingId = $request->get(ParamEnum::LISTING_ID);
        $newObservedValue = BoolHelper::isTrue($request->get(ParamEnum::OBSERVED));

        $userObservedListing = $this->em->getRepository(UserObservedListing::class)->findOneBy([
            'listing' => $listingId,
            'user' => $this->currentUserService->getUser(),
        ]);

        if ($newObservedValue && null === $userObservedListing) {
            $userObservedListing = new UserObservedListing();
            $userObservedListing->setUser($this->currentUserService->getUser());
            /** @var Listing $listing */
            $listing = $this->em->getReference(Listing::class, $listingId);
            $userObservedListing->setListing($listing);
            $userObservedListing->setDatetime(DateHelper::create());
            $this->em->persist($userObservedListing);
        }

        if ($userObservedListing && !$newObservedValue) {
            $this->em->remove($userObservedListing);
        }

        $this->em->flush();

        return $this->json([
            ParamEnum::SUCCESS => true,
            ParamEnum::OBSERVED => $newObservedValue,
        ]);
    }
}
