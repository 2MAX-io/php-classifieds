<?php

declare(strict_types=1);

namespace App\Service\Listing\Save\Dto;

use App\Entity\CustomFieldOption;
use App\Entity\Listing;
use App\Entity\ListingCustomFieldValue;
use App\Entity\Package;
use App\Service\Listing\CustomField\Dto\CustomFieldFromRequestDto;

class ListingSaveDto
{
    /**
     * @var Listing
     */
    private $listing;

    /**
     * @var Package|null
     */
    private $package;

    /**
     * @var array<string,array|int|string>
     */
    private $uploadedFilesFromRequest = [];

    /**
     * @var array<int,array|string>
     */
    private $customFieldValuesFromRequest = [];

    /**
     * @var ListingCustomFieldValue[]
     */
    private $currentValueEntities = [];

    /**
     * @var ListingCustomFieldValue[]
     */
    private $customFieldValueEntitiesToRemove = [];

    public function getListing(): Listing
    {
        return $this->listing;
    }

    public function setListing(Listing $listing): void
    {
        $this->listing = $listing;
    }

    /**
     * @return array<string,mixed>
     */
    public function getUploadedFilesFromRequest(): array
    {
        return $this->uploadedFilesFromRequest;
    }

    /**
     * @param array<string,array|int|string> $uploadedFilesFromRequest
     */
    public function setUploadedFilesFromRequest(array $uploadedFilesFromRequest): void
    {
        $this->uploadedFilesFromRequest = $uploadedFilesFromRequest;
    }

    public function hasUploadedFilesFromRequest(): bool
    {
        return !empty($this->getUploadedFilesFromRequest());
    }

    /**
     * @return array<int,array|string>
     */
    public function getCustomFieldValuesFromRequest(): array
    {
        return $this->customFieldValuesFromRequest;
    }

    /**
     * @param array<int,array|string> $customFieldValuesFromRequest
     */
    public function setCustomFieldValuesFromRequest(array $customFieldValuesFromRequest): void
    {
        $this->customFieldValuesFromRequest = $customFieldValuesFromRequest;
    }

    /**
     * @return ListingCustomFieldValue[]
     */
    public function getCurrentValueEntities(): array
    {
        return $this->currentValueEntities;
    }

    /**
     * @param ListingCustomFieldValue[] $currentValueEntities
     */
    public function setCurrentValueEntities(array $currentValueEntities): void
    {
        $this->currentValueEntities = $currentValueEntities;
    }

    /**
     * @return ListingCustomFieldValue[]
     */
    public function getCustomFieldValueEntitiesToRemove(): array
    {
        return $this->customFieldValueEntitiesToRemove;
    }

    /**
     * @param ListingCustomFieldValue[] $customFieldValueEntitiesToRemove
     */
    public function setCustomFieldValueEntitiesToRemove(array $customFieldValueEntitiesToRemove): void
    {
        $this->customFieldValueEntitiesToRemove = $customFieldValueEntitiesToRemove;
    }

    public function stopCustomFieldValueRemove(ListingCustomFieldValue $listingCustomFieldValue): void
    {
        foreach ($this->getCustomFieldValueEntitiesToRemove() as $key => $valueToRemove) {
            if ($valueToRemove->getId() === $listingCustomFieldValue->getId()) {
                unset($this->customFieldValueEntitiesToRemove[$key]);
            }
        }
    }

    public function getCurrentCustomFieldValueEntity(
        CustomFieldFromRequestDto $customFieldFromRequestDto,
        ?CustomFieldOption $option
    ): ?ListingCustomFieldValue {
        foreach ($this->getCurrentValueEntities() as $customFieldValue) {
            if ($customFieldValue->getCustomField()
                && $customFieldValue->getCustomField()->getId() === $customFieldFromRequestDto->getCustomFieldId()
            ) {
                if ($option && $customFieldValue->getCustomFieldOption()
                    && $customFieldValue->getCustomFieldOption()->getId() === $option->getId()
                ) {
                    return $customFieldValue;
                }

                if ($customFieldValue->getValue() === $customFieldFromRequestDto->getCustomFieldValueString()) {
                    return $customFieldValue;
                }
            }
        }

        return null;
    }

    public function getPackageNotNull(): Package
    {
        if (null === $this->package) {
            throw new \RuntimeException('package not found');
        }

        return $this->package;
    }

    public function getPackage(): ?Package
    {
        return $this->package;
    }

    public function setPackage(?Package $package): void
    {
        $this->package = $package;
    }
}
