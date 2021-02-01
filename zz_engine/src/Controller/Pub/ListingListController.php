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
     * @Route("/listing/search", name="app_listing_search")
     * @Route("/last-added", name="app_last_added")
     * @Route("/listings-of-user", name="app_public_listings_of_user")
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

        if (null === $listingListDto->getCategory() && $listingListDto->getRoute() === 'app_category') {
            // category not found, redirect to last added to prevent error
            return $this->redirectToRoute('app_last_added');
        }

        if ($request->query->has('user') && !\ctype_digit($request->query->get('user', false))) {
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
                'category' => $category,
                'queryParameters' => [
                    'query' => $request->query->get('query'),
                    'user' => $request->query->get('user'),
                    'min_price' => $request->query->get('min_price'),
                    'max_price' => $request->query->get('max_price'),
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
            'app_listing_search' => $this->trans->trans('trans.Search Engine'),
            'app_last_added' => $this->trans->trans('trans.Last added'),
            'app_public_listings_of_user' => $this->trans->trans('trans.Listings of user'),
            'app_category' => static function() use ($listingListDto) {
                return $listingListDto->getCategoryNotNull()->getName();
            },
        ];

        if (isset($map[$route])) {
            if (\is_callable($map[$route])) {
                return $map[$route]();
            }

            return $map[$route];
        }

        return $this->trans->trans('trans.Listings');
    }

    private function getBreadcrumbsForRoute(ListingListDto $listingListDto): ?string
    {
        $route = $listingListDto->getRoute();
        $map = [
            'app_listing_search' => $this->trans->trans('trans.Search Engine'),
            'app_last_added' => $this->trans->trans('trans.Last added'),
            'app_public_listings_of_user' => $this->trans->trans('trans.Listings of user'),
        ];

        return $map[$route] ?? null;
    }
}
