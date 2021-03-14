<?php

declare(strict_types=1);

namespace App\Service\Listing\Save;

use App\Entity\Listing;
use App\Form\ListingCustomFieldsType;
use App\Form\ListingType;
use App\Helper\DateHelper;
use App\Helper\JsonHelper;
use App\Helper\SlugHelper;
use App\Helper\StringHelper;
use App\Security\CurrentUserService;
use App\Service\Listing\CustomField\Dto\CustomFieldInlineDto;
use App\Service\Listing\Save\Dto\ListingSaveDto;
use App\Service\Listing\ValidityExtend\ValidUntilSetService;
use App\Service\Setting\SettingsService;
use App\Service\System\Text\TextService;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Minwork\Helper\Arr;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class SaveListingService
{
    /**
     * @var ValidUntilSetService
     */
    private $validUntilSetService;

    /**
     * @var TextService
     */
    private $textService;

    /**
     * @var CurrentUserService
     */
    private $currentUserService;

    /**
     * @var SettingsService
     */
    private $settingsService;

    /**
     * @var ListingFileUploadService
     */
    private $listingFileUploadService;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(
        ListingFileUploadService $listingFileUploadService,
        ValidUntilSetService $validUntilSetService,
        CurrentUserService $currentUserService,
        SettingsService $settingsService,
        TextService $textService,
        EntityManagerInterface $em
    ) {
        $this->validUntilSetService = $validUntilSetService;
        $this->textService = $textService;
        $this->currentUserService = $currentUserService;
        $this->settingsService = $settingsService;
        $this->listingFileUploadService = $listingFileUploadService;
        $this->em = $em;
    }

    public function getListingSaveDtoFromRequest(Request $request, Listing $listing = null): ListingSaveDto
    {
        $listing = $listing ?? $this->createListingForForm();

        $listingSaveDto = new ListingSaveDto();
        $listingSaveDto->setListing($listing);
        $listingSaveDto->setUploadedFilesFromRequest(
            $this->listingFileUploadService->getUploadedFilesFromRequest($request),
        );
        $listingSaveDto->setCustomFieldValuesFromRequest(
            $this->getCustomFieldValuesFromRequest($request),
        );

        return $listingSaveDto;
    }

    public function createListingForForm(): Listing
    {
        $currentDate = DateHelper::create();

        $listing = new Listing();
        $listing->setFirstCreatedDate($currentDate);
        $listing->setLastEditDate($currentDate);
        $listing->setOrderByDate($currentDate);
        $listing->setEmailShow(true);

        if ($this->currentUserService->getUserOrNull()) {
            $listing->setEmail($this->currentUserService->getUserOrNull()->getEmail());
        }

        return $listing;
    }

    /**
     * @param array<string,string|null> $listingFormDataArray
     *
     * @return array<string,string|null>
     */
    public function modifyListingPreFormSubmit(array $listingFormDataArray): array
    {
        $listingFormDataArray['description'] = $this->textService->normalizeUserInput($listingFormDataArray['description']);
        $listingFormDataArray['title'] = $this->textService->normalizeUserInput($listingFormDataArray['title']);
        $listingFormDataArray['title'] = $this->textService->removeWordsFromTitle($listingFormDataArray['title']);
        $listingFormDataArray['city'] = \ucwords(
            $this->textService->normalizeUserInput($listingFormDataArray['city']),
        );
        $listingFormDataArray['phone'] = StringHelper::replaceMultipleToSingle($listingFormDataArray['phone'], [' '], '');

        return $listingFormDataArray;
    }

    public function modifyListingPostFormSubmit(Listing $listing, FormInterface $form): void
    {
        if ($form->has('validityTimeDays')) {
            $this->validUntilSetService->setValidityDaysFromNow(
                $listing,
                (int) $form->get('validityTimeDays')->getData()
            );
        }
        $listing->setAdminActivated(false);
        if ($this->settingsService->getSettingsDto()->getRequireListingAdminActivation()) {
            $listing->setAdminRejected(false);
        }
        $this->updateSlug($listing);
        $this->saveSearchText($listing);

        if (!$listing->getUser()) { // set user when creating, if not set
            $listing->setUser($this->currentUserService->getUserOrNull());
        }

        $listing->setLastEditDate(DateHelper::create());
    }

    public function saveSearchText(Listing $listing): void
    {
        $searchText = ' ';

        $searchText .= $listing->getTitle();
        $searchText .= ' ';

        $searchText .= $listing->getDescription();
        $searchText .= ' ';

        $searchText .= $listing->getCity();
        $searchText .= ' ';

        $searchText .= $listing->getPrice();
        $searchText .= ' ';

        $category = $listing->getCategory();
        while ($category && $category->getLvl() > 0) {
            $searchText .= $category->getName();
            $searchText .= ' ';

            $category = $category->getParent();
        }

        foreach ($listing->getListingCustomFieldValues() as $listingCustomFieldValue) {
            $searchText .= $listingCustomFieldValue->getValue();
            $searchText .= ' ';

            if ($listingCustomFieldValue->getCustomFieldOption()) {
                $searchText .= $listingCustomFieldValue->getCustomFieldOption()->getName();
                $searchText .= ' ';
            }
        }

        $searchText .= $listing->getSlug();
        $searchText .= ' ';

        $searchText .= $listing->getid();
        $searchText .= ' ';

        if ($listing->getUser()) {
            $searchText .= $listing->getUser()->getId();
            $searchText .= ' ';
        }

        $listing->setSearchText($searchText);
    }

    public function saveCustomFieldsInline(Listing $listing): void
    {
        /** @var Connection|\PDO $pdo */
        $pdo = $this->em->getConnection();
        $stmt = $pdo->prepare('
            SELECT
                custom_field.name AS name,
                COALESCE(custom_field_option.name, listing_custom_field_value.value) AS value,
                custom_field.type AS type,
                custom_field.unit AS unit,
                null
            FROM listing_custom_field_value
            JOIN listing ON listing.id = listing_custom_field_value.listing_id
            JOIN custom_field ON custom_field.id = listing_custom_field_value.custom_field_id
            JOIN custom_field_for_category ON true
                && custom_field_for_category.custom_field_id = custom_field.id
                && custom_field_for_category.category_id = listing.category_id
            LEFT JOIN custom_field_option ON custom_field_option.id = listing_custom_field_value.custom_field_option_id
            WHERE true 
                && listing.id = :listing_id
                && custom_field.inline_on_list
            ORDER BY custom_field_for_category.sort ASC
            LIMIT 6
        ');
        $stmt->bindValue(':listing_id', $listing->getId());
        $stmt->setFetchMode(\PDO::FETCH_CLASS, CustomFieldInlineDto::class);
        $stmt->execute();

        $listing->setCustomFieldsInlineJson(JsonHelper::toString($stmt->fetchAll() ?: []));
    }

    public function updateSlug(Listing $listing): void
    {
        $maxSlugLength = 60;

        $slug = '';
        $slug .= $listing->getTitle();
        $slug .= ' ';
        $slug .= $listing->getCategoryNotNull()->getName();
        $slug .= ' ';
        $slug .= $listing->getCategoryNotNull()->getParentNotNull()->getName();
        $slug .= ' ';

        if (\mb_strlen($slug) < $maxSlugLength) {
            foreach ($listing->getListingCustomFieldValues() as $listingCustomFieldValue) {
                if (!$listingCustomFieldValue->getCustomFieldOption()) {
                    continue;
                }
                $slug .= $listingCustomFieldValue->getCustomFieldOption()->getName();
                $slug .= ' ';
            }
        }

        $slug = StringHelper::substrWords($slug, $maxSlugLength);
        $listing->setSlug(SlugHelper::getSlug($slug));
    }

    /**
     * from listing[customFieldList]
     *
     * @return array<int,string>
     */
    private function getCustomFieldValuesFromRequest(Request $request): array
    {
        $customFieldListArray = Arr::getNestedElement(
            $request->request->all(),
            [
                ListingType::LISTING_FIELD,
                ListingCustomFieldsType::CUSTOM_FIELD_LIST_FIELD,
            ] // listing[customFieldList]
        );

        return $customFieldListArray ?? [];
    }
}
