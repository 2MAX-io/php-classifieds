<?php

declare(strict_types=1);

namespace App\Controller\Pub\Listing;

use App\Entity\Category;
use App\Service\Category\CategoryListService;
use App\Service\Listing\ListingList\ListingListService;
use Pagerfanta\View\TwitterBootstrap4View;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
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
        RouterInterface $router,
        ListingListService $listingListService,
        CategoryListService $categoryListService,
        string $categorySlug = null
    ): Response {
        $view = new TwitterBootstrap4View();
        $page = (int) $request->get('page', 1);
        $category = null;
        if ($categorySlug) {
            $category = $this->getDoctrine()->getRepository(Category::class)->findOneBy(['slug' => $categorySlug]);
            if ($category === null) {
                throw $this->createNotFoundException();
            }
        }

        if ($request->query->has('user') && !ctype_digit($request->query->get('user', false))) {
            throw $this->createNotFoundException();
        }

        $listingListDto = $listingListService->getListings($page, $category);

        return $this->render(
            'listing_list.html.twig',
            [
                'pagination' => $view->render($listingListDto->getPager(), function (int $page) use ($router, $categorySlug, $request) {
                    return $router->generate($request->get('_route'), array_merge(
                        $request->query->all(),
                        ['categorySlug' => $categorySlug, 'page' => (int) $page]
                    ));
                },
                    [
                        'proximity' => 3,
                        'prev_message' => '&larr; ' . $this->trans->trans('trans.Previous'),
                        'next_message' => $this->trans->trans('trans.Next') . ' &rarr;',
                    ]
                ),
                'listingList' => $listingListDto->getResults(),
                'pager' => $listingListDto->getPager(),
                'customFieldList' => $listingListService->getCustomFields($category),
                'categoryList' => $categoryListService->getLevelOfSubcategoriesToDisplayForCategory($category),
                'categoryBreadcrumbs' => $categoryListService->getBreadcrumbs($category),
                'queryParameters' => [
                    'query' => $request->query->get('query'),
                    'user' => $request->query->get('user'),
                    'form_custom_field' => $request->query->get('form_custom_field'),
                ],
                'pageTitle' => $this->getPageTitleForRoute($request->get('_route')),
                'breadcrumbLast' => $this->getPageTitleForRoute($request->get('_route')),
            ]
        );
    }

    private function getPageTitleForRoute(string $route): string
    {
        $pageTitle = $this->trans->trans('trans.Listings');
        $map = [
            'app_listing_list' => $this->trans->trans('trans.Search Engine'),
            'app_last_added' => $this->trans->trans('trans.Last added'),
            'app_user_listings' => $this->trans->trans('trans.Listings of user'),
        ];

        if (isset($map[$route])) {
            $pageTitle = $map[$route];
        }

        return $pageTitle;
    }
}
