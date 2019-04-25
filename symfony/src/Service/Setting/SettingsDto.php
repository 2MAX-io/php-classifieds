<?php

declare(strict_types=1);

namespace App\Service\Setting;

class SettingsDto
{
    /**
     * @var string|null
     */
    private $indexPageTitle;

    /**
     * @var string|null
     */
    private $pageTitleSuffix;

    public function getIndexPageTitle(): ?string
    {
        return $this->indexPageTitle;
    }

    public function setIndexPageTitle(string $indexPageTitle): void
    {
        $this->indexPageTitle = $indexPageTitle;
    }

    public function getPageTitleSuffix(): ?string
    {
        return $this->pageTitleSuffix;
    }

    public function setPageTitleSuffix(?string $pageTitleSuffix): void
    {
        $this->pageTitleSuffix = $pageTitleSuffix;
    }
}
