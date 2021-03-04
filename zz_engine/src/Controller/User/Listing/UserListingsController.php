<?php

declare(strict_types=1);

namespace App\Controller\User\Listing;

use App\Controller\User\Base\AbstractUserController;
use App\Service\User\Listing\UserListingListService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserListingsController extends AbstractUserController
{
    /**
     * @Route("/user/listing/", name="app_user_listing_list")
     * @Route("/user/my-account/", name="app_user_my_account")
     */
    public function userListingsList(
        Request $request,
        UserListingListService $userListingListService
    ): Response {
        $this->dennyUnlessUser();

        $userListingListDto = $userListingListService->getList(
            (int) $request->get('page', 1),
            $request->get('query'),
        );

        return $this->render(
            'user/listing/user_listings_list.twig',
            [
                'listings' => $userListingListDto->getListings(),
                'pager' => $userListingListDto->getPager(),
            ]
        );
    }
}
