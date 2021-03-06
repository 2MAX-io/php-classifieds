<?php

declare(strict_types=1);

namespace App\Service\System\Pagination;

use App\Service\Setting\SettingsDto;
use Doctrine\ORM\QueryBuilder;
use Pagerfanta\Adapter\AdapterInterface;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\RequestStack;

class PaginationService
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var SettingsDto
     */
    private $settingsDto;

    public function __construct(RequestStack $requestStack, SettingsDto $settingsDto)
    {
        $this->requestStack = $requestStack;
        $this->settingsDto = $settingsDto;
    }

    public function createPaginationForQb(QueryBuilder $qb): Pagerfanta
    {
        $adapter = new QueryAdapter(
            $qb,
            true,
            null !== $qb->getDQLPart('having'),
        );
        $pager = new Pagerfanta($adapter);
        $pager->setMaxPerPage($this->getPerPage());
        $pager->setNormalizeOutOfRangePages(true);

        return $pager;
    }

    public function createFromAdapter(AdapterInterface $adapter): Pagerfanta
    {
        $pager = new Pagerfanta($adapter);
        $pager->setMaxPerPage($this->getPerPage());
        $pager->setNormalizeOutOfRangePages(true);

        return $pager;
    }

    public function getPerPage(): int
    {
        $default = (int) $this->settingsDto->getItemsPerPage();
        if ($default < 1) {
            $default = 10;
        }

        $request = $this->requestStack->getMasterRequest();
        if (null === $request) {
            return $default;
        }
        $fromRequest = $request->get('perPage', $default);

        return (int) \min($fromRequest, 100);
    }
}
