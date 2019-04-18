<?php

declare(strict_types=1);

namespace App\Service\System\Pagination;

use Pagerfanta\Pagerfanta;
use Pagerfanta\View\TwitterBootstrap4View;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class PaginationService
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var TranslatorInterface
     */
    private $trans;

    public function __construct(RouterInterface $router, RequestStack $requestStack, TranslatorInterface $trans)
    {
        $this->router = $router;
        $this->requestStack = $requestStack;
        $this->trans = $trans;
    }

    public function getPaginationHtml(Pagerfanta $pager): string
    {
        $view = new TwitterBootstrap4View();

        return $view->render(
            $pager,
            function (int $page) {
                return $this->router->generate(
                    $this->requestStack->getMasterRequest()->get('_route'),
                    array_merge(
                        $this->requestStack->getMasterRequest()->query->all(),
                        ['page' => (int) $page]
                    )
                );
            },
            [
                'proximity' => 5,
                'prev_message' => '&larr; ' . $this->trans->trans('trans.Previous'),
                'next_message' => $this->trans->trans('trans.Next') . ' &rarr;',
            ]
        );
    }
}
