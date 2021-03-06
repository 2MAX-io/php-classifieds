<?php

declare(strict_types=1);

namespace App\Service\Admin\Package\CategorySelection;

use App\Entity\Category;

class PackageCategoryDto
{
    /**
     * @var Category
     */
    private $category;

    /**
     * @var bool
     */
    private $selected;

    public function getCategory(): Category
    {
        return $this->category;
    }

    public function setCategory(Category $category): void
    {
        $this->category = $category;
    }

    public function isSelected(): bool
    {
        return $this->selected;
    }

    public function setSelected(bool $selected): void
    {
        $this->selected = $selected;
    }
}
