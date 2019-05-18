<?php

declare(strict_types=1);

namespace App\Service\System\Pagination;

use App\Service\Setting\SettingsService;
use Doctrine\ORM\QueryBuilder;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\RequestStack;

class PaginationService
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var SettingsService
     */
    private $settingsService;

    public function __construct(RequestStack $requestStack, SettingsService $settingsService)
    {
        $this->requestStack = $requestStack;
        $this->settingsService = $settingsService;
    }

    public function createPaginationForQb(QueryBuilder $qb): Pagerfanta
    {
        $adapter = new DoctrineORMAdapter($qb, true, $qb->getDQLPart('having') !== null);
        $pager = new Pagerfanta($adapter);
        $pager->setMaxPerPage($this->getMaxPerPage());
        $pager->setNormalizeOutOfRangePages(true);

        return $pager;
    }

    public function getMaxPerPage(): int
    {
        $default = $this->settingsService->getSettingsDto()->getItemsPerPageMax();
        if ($default < 1) {
            $default = 10;
        }

        $fromRequest = $this->requestStack->getMasterRequest()->get('perPage', $default);

        return (int) min($fromRequest, 100);
    }
}
