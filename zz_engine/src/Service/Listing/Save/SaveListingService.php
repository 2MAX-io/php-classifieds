<?php

declare(strict_types=1);

namespace App\Service\Listing\Save;

use App\Entity\Listing;
use App\Form\ListingCustomFieldListType;
use App\Form\ListingType;
use App\Helper\Json;
use App\Helper\SlugHelper;
use App\Helper\Str;
use App\Security\CurrentUserService;
use App\Service\Listing\ValidityExtend\ValidUntilSetService;
use App\Service\Setting\SettingsService;
use App\Service\System\Text\TextService;
use Minwork\Helper\Arr;
use Symfony\Component\Asset\Packages;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class SaveListingService
{
    /**
     * @var ValidUntilSetService
     */
    private $validUntilSetService;

    /**
     * @var Packages
     */
    private $packages;

    /**
     * @var TextService
     */
    private $textService;

    /**
     * @var CurrentUserService
     */
    private $currentUserService;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var SettingsService
     */
    private $settingsService;

    public function __construct(
        ValidUntilSetService $validUntilSetService,
        Packages $packages,
        TextService $textService,
        CurrentUserService $currentUserService,
        SettingsService $settingsService,
        RequestStack $requestStack
    ) {
        $this->validUntilSetService = $validUntilSetService;
        $this->packages = $packages;
        $this->textService = $textService;
        $this->currentUserService = $currentUserService;
        $this->requestStack = $requestStack;
        $this->settingsService = $settingsService;
    }

    public function createListingForForm(): Listing
    {
        $currentDate = new \DateTime();

        $listing = new Listing();
        $listing->setFirstCreatedDate($currentDate);
        $listing->setLastEditDate($currentDate);
        $listing->setOrderByDate($currentDate);
        $listing->setEmailShow(true);

        if ($this->currentUserService->getUser()) {
            $listing->setEmail($this->currentUserService->getUser()->getEmail());
        }

        return $listing;
    }

    public function setListingProperties(Listing $listing, FormInterface $form): void
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
            $listing->setUser($this->currentUserService->getUser());
        }

        $listing->setDescription(
            $this->textService->normalizeUserInput($listing->getDescription())
        );

        $listing->setTitle(
            $this->textService->normalizeUserInput($listing->getTitle())
        );

        $listing->setTitle(
            $this->textService->removeWordsFromTitle($listing->getTitle())
        );

        if ($listing->getCity()) {
            $listing->setCity(
                \ucwords(
                    $this->textService->normalizeUserInput($listing->getCity())
                )
            );
        }
    }

    public function getListingFilesForJavascript(Listing $listing): array
    {
        $fileUploaderListFilesFromRequest = $this->requestStack->getMasterRequest()->request->get('fileuploader-list-files', false);
        if ($fileUploaderListFilesFromRequest) {
            $files = Json::decodeToArray($fileUploaderListFilesFromRequest);
            $files = \array_map(function($file): array {
                if (isset($file['data']['tmpFilePath'])) {
                    $file['file'] = $this->packages->getUrl($file['data']['tmpFilePath']);
                }

                return $file;
            }, $files);

            return $files;
        }

        $returnFiles = [];
        foreach ($listing->getListingFiles() as $listingFile) {
            $returnFiles[] = [
                'name' => $listingFile->getFilename(),
                'type' => $listingFile->getMimeType(),
                'size' => $listingFile->getSizeBytes(),
                'file' => $this->packages->getUrl($listingFile->getPathInListSize()),
                'data' => [
                    'listingFileId' => $listingFile->getId(),
                    'filePath' => $this->packages->getUrl($listingFile->getPath()),
                ],
            ];
        }

        return $returnFiles;
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

        $listing->setSearchText($searchText);
    }

    public function updateSlug(Listing $listing): void
    {
        $slugSourceText = '';
        $slugSourceText .= $listing->getTitle();
        $slugSourceText .= ' ';
        $slugSourceText .= $listing->getCategory()->getName();
        $slugSourceText .= ' ';
        $slugSourceText .= $listing->getCategory()->getParent()->getName();
        $slugSourceText .= ' ';

        foreach ($listing->getListingCustomFieldValues() as $listingCustomFieldValue) {
            if (!$listingCustomFieldValue->getCustomFieldOption()) {
                continue;
            }
            $slugSourceText .= $listingCustomFieldValue->getCustomFieldOption()->getName();
            $slugSourceText .= ' ';
        }

        $slugSourceText = Str::substrWords($slugSourceText, 60);
        $listing->setSlug(SlugHelper::getSlug($slugSourceText));
    }

    /**
     * from listing[customFieldList]
     */
    public function getCustomFieldValueListArrayFromRequest(Request $request): array
    {
        $customFieldListArray = Arr::getNestedElement(
            $request->request->all(),
            [ListingType::LISTING_FIELD, ListingCustomFieldListType::CUSTOM_FIELD_LIST_FIELD] // listing[customFieldList]
        );
        return $customFieldListArray ?? [];
    }
}
