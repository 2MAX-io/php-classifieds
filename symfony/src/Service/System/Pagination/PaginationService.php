<?php

declare(strict_types=1);

namespace App\Service\System\Pagination;

use Symfony\Component\HttpFoundation\RequestStack;

class PaginationService
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function getMaxPerPage(): int
    {
        $fromRequest = $this->requestStack->getMasterRequest()->get('perPage', 10);

        return (int) min($fromRequest, 100);
    }
}
