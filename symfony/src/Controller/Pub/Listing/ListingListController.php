<?php

declare(strict_types=1);

namespace App\Controller\Pub\Listing;

use App\Entity\Category;
use App\Service\Category\CategoryListService;
use App\Service\Listing\ListingList\ListingListService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ListingListController extends AbstractController
{
    /**
     * @Route("/listing/list", name="app_listing_list")
     * @Route("/category/{categoryId}", name="app_category")
     */
    public function index(ListingListService $listingListService, CategoryListService $categoryListService, int $categoryId = null): Response
    {
        $category = null;
        if ($categoryId) {
            $category = $this->getDoctrine()->getRepository(Category::class)->find($categoryId);
            if ($category === null) {
                throw $this->createNotFoundException();
            }
        }

        return $this->render(
            'listing_list.html.twig',
            [
                'listingList' => $listingListService->getListings($category),
                'customFieldList' => $listingListService->getCustomFields(),
                'categoryList' => $categoryListService->getLevelOfSubcategoriesToDisplayForCategory($categoryId),
            ]
        );
    }
}
