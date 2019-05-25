<?php

declare(strict_types=1);

namespace App\Controller\Pub;

use App\Repository\CategoryRepository;
use App\Service\Category\CategoryListService;
use App\Service\Listing\ListingList\ListingListDto;
use App\Service\Listing\ListingList\ListingListService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class ListingListController extends AbstractController
{
    /**
     * @var TranslatorInterface
     */
    private $trans;

    public function __construct(TranslatorInterface $trans)
    {
        $this->trans = $trans;
    }

    /**
     * @Route("/listing/list", name="app_listing_list")
     * @Route("/last-added", name="app_last_added")
     * @Route("/listings-of-user", name="app_user_listings")
     * @Route("/c/{categorySlug}", name="app_category")
     */
    public function index(
        Request $request,
        ListingListService $listingListService,
        CategoryListService $categoryListService,
        CategoryRepository $categoryRepository,
        string $categorySlug = null
    ): Response {
        $listingListDto = new ListingListDto();
        $listingListDto->setRoute($request->get('_route'));
        $page = (int) $request->get('page', 1);
        $listingListDto->setPageNumber($page);

        $category = null;
        if ($categorySlug) {
            $category = $categoryRepository->findOneBy(['slug' => $categorySlug]);
            if ($category === null) {
                throw $this->createNotFoundException();
            }
            $listingListDto->setCategory($category);
        }

        if ($request->query->has('user') && !ctype_digit($request->query->get('user', false))) {
            throw $this->createNotFoundException();
        }

        if ($listingListDto->getRoute() === 'app_last_added') {
            $listingListDto->setLastAddedListFlag(true);
        }

        $routeParams = [
            'categorySlug' => $categorySlug,
        ];

        $customFieldsForCategory = $listingListService->getCustomFields($category);
        $listingListDto->setCustomFieldForCategoryList($customFieldsForCategory);
        $listingListDto = $listingListService->getListings($listingListDto);

        if ($listingListDto->getRedirectToPageNumber()) {
            return $this->redirectToRoute(
                $listingListDto->getRoute(),
                \array_replace(
                    $routeParams,
                    $request->query->all(),
                    [
                        'page' => $listingListDto->getRedirectToPageNumber(),
                    ]
                ),
                Response::HTTP_TEMPORARY_REDIRECT
            );
        }
        return $this->render(
            'listing_list.html.twig',
            [
                'listingList' => $listingListDto->getResults(),
                'pager' => $listingListDto->getPager(),
                'pager_route_params' => $routeParams,
                'listingListDto' => $listingListDto,
                'customFieldList' => $customFieldsForCategory,
                'categoryList' => $categoryListService->getLevelOfSubcategoriesToDisplayForCategory($category),
                'categoryBreadcrumbs' => $categoryListService->getBreadcrumbs($category),
                'queryParameters' => [
                    'query' => $request->query->get('query'),
                    'user' => $request->query->get('user'),
                    'form_custom_field' => $request->query->get('form_custom_field'),
                ],
                'pageTitle' => $this->getPageTitleForRoute($listingListDto),
                'breadcrumbLast' => $this->getBreadcrumbsForRoute($listingListDto),
            ]
        );
    }

    private function getPageTitleForRoute(ListingListDto $listingListDto): string
    {
        $route = $listingListDto->getRoute();
        $map = [
            'app_listing_list' => $this->trans->trans('trans.Search Engine'),
            'app_last_added' => $this->trans->trans('trans.Last added'),
            'app_user_listings' => $this->trans->trans('trans.Listings of user'),
            'app_category' => function() use ($listingListDto) {
                return $listingListDto->getCategory()->getName();
            },
        ];

        if (isset($map[$route])) {
            if (\is_callable($map[$route])) {
                return $map[$route]();
            } else {
                return $map[$route];
            }
        } else {
            return $this->trans->trans('trans.Listings');
        }
    }

    private function getBreadcrumbsForRoute(ListingListDto $listingListDto): ?string
    {
        $route = $listingListDto->getRoute();
        $map = [
            'app_listing_list' => $this->trans->trans('trans.Search Engine'),
            'app_last_added' => $this->trans->trans('trans.Last added'),
            'app_user_listings' => $this->trans->trans('trans.Listings of user'),
        ];

        if (isset($map[$route])) {
            return $map[$route];
        } else {
            return null;
        }
    }
}