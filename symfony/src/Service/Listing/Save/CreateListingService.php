<?php

declare(strict_types=1);

namespace App\Service\Listing\Save;

use App\Entity\Listing;
use App\Service\Listing\ValidityExtend\ValidUntilSetService;
use Ausi\SlugGenerator\SlugGenerator;
use Ausi\SlugGenerator\SlugOptions;
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
        $listing->setEmailShow(true);

        return $listing;
    }

    public function setFormDependent(Listing $listing, FormInterface $form): void
    {
        if ($form->has('validityTimeDays')) {
            $this->validUntilSetService->setValidUntil($listing, (int) $form->get('validityTimeDays')->getData());
        }

        $listing->setAdminConfirmed(false);
        $listing->setAdminRejected(false);
        $this->updateSlug($listing);
        $this->saveSearchText($listing);
    }

    public function getListingFilesForJavascript(Listing $listing): array
    {
        $returnFiles = [];
        foreach ($listing->getListingFiles() as $listingFile) {
            $returnFiles[] = [
                'name' => $listingFile->getFilename(),
                'type' => $listingFile->getMimeType(),
                'size' => $listingFile->getSizeBytes(),
                'file' => $this->packages->getUrl($listingFile->getPathInListSize()),
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

        $searchText .= $listing->getSlug();
        $searchText .= ' ';

        $listing->setSearchText($searchText);
    }

    public function updateSlug(Listing $listing): void
    {
        $generator = new SlugGenerator(
            (new SlugOptions)
                ->setValidChars('a-z0-9')
                ->setDelimiter('-')
        );

        $slugSourceText = '';
        $slugSourceText .= $listing->getTitle();
        $slugSourceText .= ' ';
        $slugSourceText .= $listing->getCategory()->getName();
        $slugSourceText .= ' ';
        $slugSourceText .= $listing->getCategory()->getParent()->getName();
        $slugSourceText .= ' ';

        foreach ($listing->getListingCustomFieldValues() as $listingCustomFieldValue) {
            $slugSourceText .= $listingCustomFieldValue->getValue();
            $slugSourceText .= ' ';
        }

        $slugSourceText = mb_substr($slugSourceText, 0, 60);
        $slug = $generator->generate(trim($slugSourceText));
        $listing->setSlug($slug);
    }
}
