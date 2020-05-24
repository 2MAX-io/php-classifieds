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
    private $footerSiteCopyright;

    /**
     * @var string|null
     */
    private $emailFromAddress;

    /**
     * @var string|null
     */
    private $emailFromName;

    /**
     * @var string|null
     */
    private $emailReplyTo;

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
     * @var string|null
     */
    private $linkContact;

    /**
     * @var string|null
     */
    private $linkAdvertisement;

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
     * @var bool|null
     */
    private $allowedCharactersEnabled;

    /**
     * @var string|null
     */
    private $allowedCharacters;

    /**
     * @var string|null
     */
    private $wordsToRemoveFromTitle;

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

    /**
     * @var bool|null
     */
    private $requireListingAdminActivation;

    /**
     * @var boolean
     */
    private $paymentAllowed;

    /**
     * @var null|string
     */
    private $paymentGateway;

    /**
     * @var string|null
     */
    private $paymentGatewayPaymentDescription;

    /**
     * @var string|null
     */
    private $paymentPayPalMode;

    /**
     * @var string|null
     */
    private $paymentPayPalClientId;

    /**
     * @var string|null
     */
    private $paymentPayPalClientSecret;

    /**
     * @var null|string
     */
    private $paymentPrzelewy24Mode;

    /**
     * @var null|string
     */
    private $paymentPrzelewy24MerchantId;

    /**
     * @var null|string
     */
    private $paymentPrzelewy24PosId;

    /**
     * @var null|string
     */
    private $paymentPrzelewy24Crc;

    /**
     * @var string|null
     */
    private $itemsPerPageMax;

    /**
     * @var string|null
     */
    private $logoPath;

    /**
     * @var string|null
     */
    private $customJavascriptBottom;

    /**
     * @var string|null
     */
    private $customJavascriptInHead;

    /**
     * @var string|null
     */
    private $customCss;

    /**
     * @var string|null
     */
    private $license;

    /**
     * @var string|null
     */
    private $facebookSignInEnabled;

    /**
     * @var string|null
     */
    private $facebookSignInAppId;

    /**
     * @var string|null
     */
    private $facebookSignInAppSecret;

    /**
     * @var string|null
     */
    private $googleSignInEnabled;

    /**
     * @var string|null
     */
    private $googleSignInClientId;

    /**
     * @var string|null
     */
    private $googleSignInClientSecret;

    /**
     * @var string|null
     */
    private $invoiceCompanyName;

    /**
     * @var string|null
     */
    private $invoiceTaxNumber;

    /**
     * @var string|null
     */
    private $invoiceCity;

    /**
     * @var string|null
     */
    private $invoiceStreet;

    /**
     * @var string|null
     */
    private $invoiceBuildingNumber;

    /**
     * @var string|null
     */
    private $invoiceUnitNumber;

    /**
     * @var string|null
     */
    private $invoiceZipCode;

    /**
     * @var string|null
     */
    private $invoiceCountry;

    /**
     * @var string|null
     */
    private $invoiceEmail;

    /**
     * @var string|null
     */
    private $invoiceSoldItemDescription;

    /**
     * @var string|null
     */
    private $invoiceGenerationStrategy;

    /**
     * @var string|null
     */
    private $invoiceNumberPrefix;

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

    public function getAllowedCharactersEnabled(): ?bool
    {
        return $this->allowedCharactersEnabled ?? false;
    }

    public function setAllowedCharactersEnabled(?bool $allowedCharactersEnabled): void
    {
        $this->allowedCharactersEnabled = $allowedCharactersEnabled;
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

    public function getMasterSiteLinkShow(): bool
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

    public function getRequireListingAdminActivation(): ?bool
    {
        return $this->requireListingAdminActivation;
    }

    public function setRequireListingAdminActivation(?bool $requireListingAdminActivation): void
    {
        $this->requireListingAdminActivation = $requireListingAdminActivation;
    }

    public function getPaymentPayPalMode(): ?string
    {
        return $this->paymentPayPalMode;
    }

    public function setPaymentPayPalMode(?string $paymentPayPalMode): void
    {
        $this->paymentPayPalMode = $paymentPayPalMode;
    }

    public function getPaymentPayPalClientId(): ?string
    {
        return $this->paymentPayPalClientId;
    }

    public function setPaymentPayPalClientId(?string $paymentPayPalClientId): void
    {
        $this->paymentPayPalClientId = $paymentPayPalClientId;
    }

    public function getPaymentPayPalClientSecret(): ?string
    {
        return $this->paymentPayPalClientSecret;
    }

    public function setPaymentPayPalClientSecret(?string $paymentPayPalClientSecret): void
    {
        $this->paymentPayPalClientSecret = $paymentPayPalClientSecret;
    }

    public function getPaymentGatewayPaymentDescription(): ?string
    {
        return $this->paymentGatewayPaymentDescription;
    }

    public function setPaymentGatewayPaymentDescription(?string $paymentGatewayPaymentDescription): void
    {
        $this->paymentGatewayPaymentDescription = $paymentGatewayPaymentDescription;
    }

    public function getWordsToRemoveFromTitle(): ?string
    {
        return $this->wordsToRemoveFromTitle;
    }

    public function setWordsToRemoveFromTitle(?string $wordsToRemoveFromTitle): void
    {
        $this->wordsToRemoveFromTitle = $wordsToRemoveFromTitle;
    }

    public function getFooterSiteCopyright(): ?string
    {
        return $this->footerSiteCopyright;
    }

    public function setFooterSiteCopyright(?string $footerSiteCopyright): void
    {
        $this->footerSiteCopyright = $footerSiteCopyright;
    }

    public function getItemsPerPageMax(): ?string
    {
        return $this->itemsPerPageMax;
    }

    public function setItemsPerPageMax(?string $itemsPerPageMax): void
    {
        $this->itemsPerPageMax = $itemsPerPageMax;
    }

    public function getEmailFromName(): ?string
    {
        return $this->emailFromName;
    }

    public function setEmailFromName(?string $emailFromName): void
    {
        $this->emailFromName = $emailFromName;
    }

    public function getEmailReplyTo(): ?string
    {
        return $this->emailReplyTo;
    }

    public function setEmailReplyTo(?string $emailReplyTo): void
    {
        $this->emailReplyTo = $emailReplyTo;
    }

    public function getEmailFromAddress(): ?string
    {
        return $this->emailFromAddress;
    }

    public function setEmailFromAddress(?string $emailFromAddress): void
    {
        $this->emailFromAddress = $emailFromAddress;
    }

    public function getLogoPath(): ?string
    {
        return $this->logoPath;
    }

    public function setLogoPath(?string $logoPath): void
    {
        $this->logoPath = $logoPath;
    }

    public function getLinkContact(): ?string
    {
        return $this->linkContact;
    }

    public function setLinkContact(?string $linkContact): void
    {
        $this->linkContact = $linkContact;
    }

    public function getLinkAdvertisement(): ?string
    {
        return $this->linkAdvertisement;
    }

    public function setLinkAdvertisement(?string $linkAdvertisement): void
    {
        $this->linkAdvertisement = $linkAdvertisement;
    }

    public function getCustomJavascriptBottom(): ?string
    {
        return $this->customJavascriptBottom;
    }

    public function setCustomJavascriptBottom(?string $customJavascriptBottom): void
    {
        $this->customJavascriptBottom = $customJavascriptBottom;
    }

    public function getCustomJavascriptInHead(): ?string
    {
        return $this->customJavascriptInHead;
    }

    public function setCustomJavascriptInHead(?string $customJavascriptInHead): void
    {
        $this->customJavascriptInHead = $customJavascriptInHead;
    }

    public function getCustomCss(): ?string
    {
        return $this->customCss;
    }

    public function setCustomCss(?string $customCss): void
    {
        $this->customCss = $customCss;
    }

    public function isPaymentAllowed(): bool
    {
        return $this->paymentAllowed ?? false;
    }

    public function setPaymentAllowed(bool $paymentAllowed): void
    {
        $this->paymentAllowed = $paymentAllowed;
    }

    public function getLicense(): ?string
    {
        return $this->license;
    }

    public function setLicense(?string $license): void
    {
        $this->license = $license;
    }

    public function getFacebookSignInAppId(): ?string
    {
        return $this->facebookSignInAppId;
    }

    public function setFacebookSignInAppId(?string $facebookSignInAppId): void
    {
        $this->facebookSignInAppId = $facebookSignInAppId;
    }

    public function getFacebookSignInAppSecret(): ?string
    {
        return $this->facebookSignInAppSecret;
    }

    public function setFacebookSignInAppSecret(?string $facebookSignInAppSecret): void
    {
        $this->facebookSignInAppSecret = $facebookSignInAppSecret;
    }

    public function getGoogleSignInClientId(): ?string
    {
        return $this->googleSignInClientId;
    }

    public function setGoogleSignInClientId(?string $googleSignInClientId): void
    {
        $this->googleSignInClientId = $googleSignInClientId;
    }

    public function getGoogleSignInClientSecret(): ?string
    {
        return $this->googleSignInClientSecret;
    }

    public function setGoogleSignInClientSecret(?string $googleSignInClientSecret): void
    {
        $this->googleSignInClientSecret = $googleSignInClientSecret;
    }

    public function getFacebookSignInEnabled(): ?string
    {
        return $this->facebookSignInEnabled;
    }

    public function setFacebookSignInEnabled(?string $facebookSignInEnabled): void
    {
        $this->facebookSignInEnabled = $facebookSignInEnabled;
    }

    public function getGoogleSignInEnabled(): ?string
    {
        return $this->googleSignInEnabled;
    }

    public function setGoogleSignInEnabled(?string $googleSignInEnabled): void
    {
        $this->googleSignInEnabled = $googleSignInEnabled;
    }

    public function getPaymentGateway(): ?string
    {
        return $this->paymentGateway;
    }

    public function setPaymentGateway(?string $paymentGateway): void
    {
        $this->paymentGateway = $paymentGateway;
    }

    public function getPaymentPrzelewy24MerchantId(): ?string
    {
        return $this->paymentPrzelewy24MerchantId;
    }

    public function setPaymentPrzelewy24MerchantId(?string $paymentPrzelewy24MerchantId): void
    {
        $this->paymentPrzelewy24MerchantId = $paymentPrzelewy24MerchantId;
    }

    public function getPaymentPrzelewy24PosId(): ?string
    {
        return $this->paymentPrzelewy24PosId;
    }

    public function setPaymentPrzelewy24PosId(?string $paymentPrzelewy24PosId): void
    {
        $this->paymentPrzelewy24PosId = $paymentPrzelewy24PosId;
    }

    public function getPaymentPrzelewy24Crc(): ?string
    {
        return $this->paymentPrzelewy24Crc;
    }

    public function setPaymentPrzelewy24Crc(?string $paymentPrzelewy24Crc): void
    {
        $this->paymentPrzelewy24Crc = $paymentPrzelewy24Crc;
    }

    public function getPaymentPrzelewy24Mode(): ?string
    {
        return $this->paymentPrzelewy24Mode;
    }

    public function setPaymentPrzelewy24Mode(?string $paymentPrzelewy24Mode): void
    {
        $this->paymentPrzelewy24Mode = $paymentPrzelewy24Mode;
    }

    public function getInvoiceCompanyName(): ?string
    {
        return $this->invoiceCompanyName;
    }

    public function setInvoiceCompanyName(?string $invoiceCompanyName): void
    {
        $this->invoiceCompanyName = $invoiceCompanyName;
    }

    public function getInvoiceTaxNumber(): ?string
    {
        return $this->invoiceTaxNumber;
    }

    public function setInvoiceTaxNumber(?string $invoiceTaxNumber): void
    {
        $this->invoiceTaxNumber = $invoiceTaxNumber;
    }

    public function getInvoiceCity(): ?string
    {
        return $this->invoiceCity;
    }

    public function setInvoiceCity(?string $invoiceCity): void
    {
        $this->invoiceCity = $invoiceCity;
    }

    public function getInvoiceStreet(): ?string
    {
        return $this->invoiceStreet;
    }

    public function setInvoiceStreet(?string $invoiceStreet): void
    {
        $this->invoiceStreet = $invoiceStreet;
    }

    public function getInvoiceBuildingNumber(): ?string
    {
        return $this->invoiceBuildingNumber;
    }

    public function setInvoiceBuildingNumber(?string $invoiceBuildingNumber): void
    {
        $this->invoiceBuildingNumber = $invoiceBuildingNumber;
    }

    public function getInvoiceUnitNumber(): ?string
    {
        return $this->invoiceUnitNumber;
    }

    public function setInvoiceUnitNumber(?string $invoiceUnitNumber): void
    {
        $this->invoiceUnitNumber = $invoiceUnitNumber;
    }

    public function getInvoiceZipCode(): ?string
    {
        return $this->invoiceZipCode;
    }

    public function setInvoiceZipCode(?string $invoiceZipCode): void
    {
        $this->invoiceZipCode = $invoiceZipCode;
    }

    public function getInvoiceCountry(): ?string
    {
        return $this->invoiceCountry;
    }

    public function setInvoiceCountry(?string $invoiceCountry): void
    {
        $this->invoiceCountry = $invoiceCountry;
    }

    public function getInvoiceEmail(): ?string
    {
        return $this->invoiceEmail;
    }

    public function setInvoiceEmail(?string $invoiceEmail): void
    {
        $this->invoiceEmail = $invoiceEmail;
    }

    public function getInvoiceSoldItemDescription(): ?string
    {
        return $this->invoiceSoldItemDescription;
    }

    public function setInvoiceSoldItemDescription(?string $invoiceSoldItemDescription): void
    {
        $this->invoiceSoldItemDescription = $invoiceSoldItemDescription;
    }

    public function getInvoiceGenerationStrategy(): ?string
    {
        return $this->invoiceGenerationStrategy;
    }

    public function setInvoiceGenerationStrategy(?string $invoiceGenerationStrategy): void
    {
        $this->invoiceGenerationStrategy = $invoiceGenerationStrategy;
    }

    public function getInvoiceNumberPrefix(): ?string
    {
        return $this->invoiceNumberPrefix;
    }

    public function setInvoiceNumberPrefix(?string $invoiceNumberPrefix): void
    {
        $this->invoiceNumberPrefix = $invoiceNumberPrefix;
    }
}
