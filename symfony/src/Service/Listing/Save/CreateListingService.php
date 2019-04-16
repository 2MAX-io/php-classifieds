<?php

declare(strict_types=1);

namespace App\Service\Listing\Save;

use App\Entity\Listing;
use App\Service\Listing\ValidityExtend\ValidUntilSetService;
use DateTime;
use Symfony\Component\Asset\Packages;
use Symfony\Component\Form\FormInterface;

class CreateListingService
{
    /**
     * @var ValidUntilSetService
     */
    private $validUntilSetService;

    /**
     * @var Packages
     */
    private $packages;

    public function __construct(ValidUntilSetService $validUntilSetService, Packages $packages)
    {
        $this->validUntilSetService = $validUntilSetService;
        $this->packages = $packages;
    }

    public function create(): Listing
    {
        $listing = new Listing();
        $listing->setFirstCreatedDate(new DateTime());
        $listing->setLastEditDate(new DateTime());
        $listing->setOrderByDate(new DateTime());

        return $listing;
    }

    public function setFormDependent(Listing $listing, FormInterface $form): void
    {
        if ($form->has('validityTimeDays')) {
            $this->validUntilSetService->setValidUntil($listing, (int) $form->get('validityTimeDays')->getData());
        }

        $listing->setAdminConfirmed(false);

        $this->saveSearchText($listing);
    }

    public function getListingFilesForJavascript(Listing $listing): array
    {
        $returnFiles = [];
        foreach ($listing->getListingFiles() as $listingFile) {
            $returnFiles[] = [
                'name' => basename($listingFile->getPath()),
                'type' => mime_content_type($listingFile->getPath()),
                'size' => filesize($listingFile->getPath()),
                'file' => $this->packages->getUrl($listingFile->getPath()),
                'data' => [
                    'listingFileId' => $listingFile->getId(),
                ],
            ];
        }

        return $returnFiles;
    }

    public function saveSearchText(Listing $listing)
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

        $listing->setSearchText($searchText);
    }
}
