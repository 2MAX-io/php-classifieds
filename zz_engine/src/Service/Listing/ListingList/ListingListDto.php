<?php

declare(strict_types=1);

namespace App\Service\Listing\ListingList;

use App\Entity\Category;
use App\Entity\CustomField;
use App\Entity\Listing;
use App\Entity\User;
use Pagerfanta\Pagerfanta;

class ListingListDto
{
    /**
     * @var Listing[]|iterable
     */
    private $results;

    /**
     * @var Pagerfanta
     */
    private $pager;

    /**
     * @var Category
     */
    private $category;

    /**
     * @var int
     */
    private $pageNumber;

    /**
     * @var boolean
     */
    private $lastAddedListFlag = false;

    /**
     * @var string
     */
    private $route;

    /**
     * @var int|null
     */
    private $redirectToPageNumber;

    /**
     * @var CustomField[]
     */
    private $customFieldForCategoryList;

    /**
     * @var User|null
     */
    private $filterByUser;

    /**
     * @var string|null
     */
    private $categorySlug;

    /**
     * @var string|null
     */
    private $redirectToRoute;

    /**
     * @return \Traversable|Listing[]
     */
    public function getResults(): \Traversable
    {
        return $this->results;
    }

    public function getPager(): Pagerfanta
    {
        return $this->pager;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function getCategoryNotNull(): Category
    {
        return $this->category;
    }

    public function setCategory(Category $category): void
    {
        $this->category = $category;
    }

    public function setPager(Pagerfanta $pager): void
    {
        $this->pager = $pager;
    }

    /**
     * @param Listing[]|iterable $results
     */
    public function setResults(iterable $results): void
    {
        $this->results = $results;
    }

    public function getPageNumber(): int
    {
        return $this->pageNumber;
    }

    public function setPageNumber(int $pageNumber): void
    {
        $this->pageNumber = $pageNumber;
    }

    public function isLastAddedListFlag(): bool
    {
        return $this->lastAddedListFlag;
    }

    public function setLastAddedListFlag(bool $lastAddedListFlag): void
    {
        $this->lastAddedListFlag = $lastAddedListFlag;
    }

    public function getRoute(): string
    {
        return $this->route;
    }

    public function setRoute(string $route): void
    {
        $this->route = $route;
    }

    public function getRedirectToPageNumber(): ?int
    {
        return $this->redirectToPageNumber;
    }

    public function setRedirectToPageNumber(?int $redirectToPageNumber): void
    {
        $this->redirectToPageNumber = $redirectToPageNumber;
    }

    public function getCustomFieldForCategoryList(): array
    {
        return $this->customFieldForCategoryList;
    }

    public function setCustomFieldForCategoryList(array $customFieldForCategoryList): void
    {
        $this->customFieldForCategoryList = $customFieldForCategoryList;
    }

    public function getFilterByUser(): ?User
    {
        return $this->filterByUser;
    }

    public function setFilterByUser(?User $filterByUser): void
    {
        $this->filterByUser = $filterByUser;
    }

    public function getCategorySlug(): ?string
    {
        return $this->categorySlug;
    }

    public function setCategorySlug(?string $categorySlug): void
    {
        $this->categorySlug = $categorySlug;
    }

    public function getRedirectToRoute(): ?string
    {
        return $this->redirectToRoute;
    }

    public function setRedirectToRoute(?string $redirectToRoute): void
    {
        $this->redirectToRoute = $redirectToRoute;
    }
}
