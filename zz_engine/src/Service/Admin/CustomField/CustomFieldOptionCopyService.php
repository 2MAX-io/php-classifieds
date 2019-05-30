<?php

declare(strict_types=1);

namespace App\Service\Admin\CustomField;

use App\Entity\CustomField;
use App\Entity\CustomFieldOption;
use App\Form\Admin\CustomFieldOptionCopyDto;
use App\Helper\Arr;
use App\Service\System\Sort\SortService;
use Doctrine\ORM\EntityManagerInterface;

class CustomFieldOptionCopyService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function copy(
        CustomFieldOptionCopyDto $customFieldOptionCopyDto,
        CustomField $targetCustomField
    ): void {
        $currentOptions = $this->getOptionsIndexedByValue($targetCustomField);
        $sort = $targetCustomField->getCustomFieldOptions()->count() ? $targetCustomField->getCustomFieldOptions()->last()->getSort(): SortService::START_REORDER_FROM;

        foreach ($customFieldOptionCopyDto->getSourceCustomField()->getCustomFieldOptions() as $sourceCustomFieldOption) {
            $sort++;
            $newValue = $sourceCustomFieldOption->getValue();
            if (isset($currentOptions[$newValue])) {
                continue;
            }

            $newCustomFieldOption= clone $sourceCustomFieldOption;
            $newCustomFieldOption->setCustomField($targetCustomField);
            $newCustomFieldOption->setSort($sort);
            $targetCustomField->addCustomFieldOption($newCustomFieldOption);
            $this->em->persist($newCustomFieldOption);
        }
    }

    private function getOptionsIndexedByValue(CustomField $targetCustomField): array
    {
        return Arr::indexBy($targetCustomField->getCustomFieldOptions()->toArray(), function(CustomFieldOption $customFieldOption) {
            return [$customFieldOption->getValue() => $customFieldOption];
        });
    }
}
