<?php

declare(strict_types=1);

namespace App\Form\Admin\ExecuteAction;

use App\Entity\Category;
use App\Entity\CustomFieldOption;
use App\Service\Admin\Listing\Dto\AdminListingListDto;

class ExecuteActionDto
{
    /**
     * @var string|null
     */
    private $action;

    /**
     * @var CustomFieldOption|null
     */
    private $customFieldOption;

    /**
     * @var Category|null
     */
    private $category;

    /**
     * @var AdminListingListDto
     */
    private $adminListingListDto;

    public function getAction(): ?string
    {
        return $this->action;
    }

    public function setAction(?string $action): void
    {
        $this->action = $action;
    }

    public function getCustomFieldOption(): ?CustomFieldOption
    {
        return $this->customFieldOption;
    }

    public function setCustomFieldOption(?CustomFieldOption $customFieldOption): void
    {
        $this->customFieldOption = $customFieldOption;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): void
    {
        $this->category = $category;
    }

    public function getAdminListingListDto(): AdminListingListDto
    {
        return $this->adminListingListDto;
    }

    public function setAdminListingListDto(AdminListingListDto $adminListingListDto): void
    {
        $this->adminListingListDto = $adminListingListDto;
    }
}
