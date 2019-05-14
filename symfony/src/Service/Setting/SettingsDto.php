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

    /**
     * @var string|null
     */
    private $metaDescription;

    /**
     * @var string|null
     */
    private $metaKeywords;

    /**
     * @var string|null
     */
    private $rssTitle;

    /**
     * @var string|null
     */
    private $rssDescription;

    /**
     * @var string|null
     */
    private $linkTermsConditions;

    /**
     * @var string|null
     */
    private $linkPrivacyPolicy;

    /**
     * @var string|null
     */
    private $linkRejectionReason;

    /**
     * ISO 639-1
     *
     * @var string|null
     */
    private $languageTwoLetters;

    /**
     * @var string|null
     */
    private $currency;

    /**
     * @var string|null
     */
    private $allowedCharacters;

    /**
     * @var string|null
     */
    private $searchPlaceholder;

    /**
     * @var boolean
     */
    private $masterSiteLinkShow;

    /**
     * @var string|null
     */
    private $masterSiteUrl;

    /**
     * @var string|null
     */
    private $masterSiteAnchorText;

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

    public function getRssTitle(): ?string
    {
        return $this->rssTitle;
    }

    public function setRssTitle(?string $rssTitle): void
    {
        $this->rssTitle = $rssTitle;
    }

    public function getRssDescription(): ?string
    {
        return $this->rssDescription;
    }

    public function setRssDescription(?string $rssDescription): void
    {
        $this->rssDescription = $rssDescription;
    }

    public function getMetaDescription(): ?string
    {
        return $this->metaDescription;
    }

    public function setMetaDescription(?string $metaDescription): void
    {
        $this->metaDescription = $metaDescription;
    }

    public function getMetaKeywords(): ?string
    {
        return $this->metaKeywords;
    }

    public function setMetaKeywords(?string $metaKeywords): void
    {
        $this->metaKeywords = $metaKeywords;
    }

    public function getLinkTermsConditions(): ?string
    {
        return $this->linkTermsConditions;
    }

    public function setLinkTermsConditions(?string $linkTermsConditions): void
    {
        $this->linkTermsConditions = $linkTermsConditions;
    }

    public function getLanguageTwoLetters(): ?string
    {
        return $this->languageTwoLetters;
    }

    public function setLanguageTwoLetters(?string $languageTwoLetters): void
    {
        $this->languageTwoLetters = $languageTwoLetters;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function setCurrency(?string $currency): void
    {
        $this->currency = $currency;
    }

    public function getAllowedCharacters(): ?string
    {
        return $this->allowedCharacters;
    }

    public function setAllowedCharacters(?string $allowedCharacters): void
    {
        $this->allowedCharacters = $allowedCharacters;
    }

    public function getSearchPlaceholder(): ?string
    {
        return $this->searchPlaceholder;
    }

    public function setSearchPlaceholder(?string $searchPlaceholder): void
    {
        $this->searchPlaceholder = $searchPlaceholder;
    }

    public function getMasterSiteUrl(): ?string
    {
        return $this->masterSiteUrl;
    }

    public function setMasterSiteUrl(?string $masterSiteUrl): void
    {
        $this->masterSiteUrl = $masterSiteUrl;
    }

    public function getMasterSiteAnchorText(): ?string
    {
        return $this->masterSiteAnchorText;
    }

    public function setMasterSiteAnchorText(?string $masterSiteAnchorText): void
    {
        $this->masterSiteAnchorText = $masterSiteAnchorText;
    }

    public function isMasterSiteLinkShow(): bool
    {
        return $this->masterSiteLinkShow ?? false;
    }

    public function setMasterSiteLinkShow(bool $masterSiteLinkShow): void
    {
        $this->masterSiteLinkShow = $masterSiteLinkShow;
    }

    public function getLinkPrivacyPolicy(): ?string
    {
        return $this->linkPrivacyPolicy;
    }

    public function setLinkPrivacyPolicy(?string $linkPrivacyPolicy): void
    {
        $this->linkPrivacyPolicy = $linkPrivacyPolicy;
    }

    public function getLinkRejectionReason(): ?string
    {
        return $this->linkRejectionReason;
    }

    public function setLinkRejectionReason(?string $linkRejectionReason): void
    {
        $this->linkRejectionReason = $linkRejectionReason;
    }
}
