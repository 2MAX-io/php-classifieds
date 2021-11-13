<?php

declare(strict_types=1);

namespace App\Service\Listing\Save;

use App\Entity\Listing;
use App\Form\Listing\ListingCustomFieldsType;
use App\Form\Listing\ListingType;
use App\Helper\DateHelper;
use App\Helper\JsonHelper;
use App\Helper\SlugHelper;
use App\Helper\StringHelper;
use App\Security\CurrentUserService;
use App\Service\Listing\CustomField\Dto\CustomFieldInlineDto;
use App\Service\Listing\Package\ApplyPackageToListingService;
use App\Service\Listing\Save\Dto\ListingSaveDto;
use App\Service\Setting\SettingsDto;
use App\Service\System\Text\TextService;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Minwork\Helper\Arr;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class SaveListingService
{
    /**
     * @var TextService
     */
    private $textService;

    /**
     * @var ApplyPackageToListingService
     */
    private $setPackageToListingService;

    /**
     * @var CurrentUserService
     */
    private $currentUserService;

    /**
     * @var SettingsDto
     */
    private $settingsDto;

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
        ApplyPackageToListingService $setPackageToListingService,
        CurrentUserService $currentUserService,
        SettingsDto $settingsDto,
        TextService $textService,
        EntityManagerInterface $em
    ) {
        $this->listingFileUploadService = $listingFileUploadService;
        $this->setPackageToListingService = $setPackageToListingService;
        $this->currentUserService = $currentUserService;
        $this->settingsDto = $settingsDto;
        $this->textService = $textService;
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
            $listing->setUser($this->currentUserService->getUserOrNull());
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
        $listingFormDataArray['location'] = ucwords(
            $this->textService->normalizeUserInput($listingFormDataArray['location']),
        );
        $listingFormDataArray['phone'] = StringHelper::replaceMultipleToSingle($listingFormDataArray['phone'], [' '], '');

        return $listingFormDataArray;
    }

    public function modifyListingPostFormSubmit(ListingSaveDto $listingSaveDto, FormInterface $form): void
    {
        $listing = $listingSaveDto->getListing();
        $this->setPackageToListingService->applyPackageToListing(
            $listingSaveDto->getListing(),
            $listingSaveDto->getPackage(),
        );

        $listing->setUserDeactivated(false);
        $listing->setAdminActivated(false);
        if ($this->settingsDto->getRequireListingAdminActivation()) {
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

        $searchText .= $listing->getLocation();
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
        /** @var \PDO $pdo */
        $pdo = $this->em->getConnection()->getWrappedConnection()->getWrappedConnection();
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

        if (mb_strlen($slug) < $maxSlugLength) {
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
