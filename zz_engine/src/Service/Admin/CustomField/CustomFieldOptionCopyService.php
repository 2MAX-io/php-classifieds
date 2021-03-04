<?php

declare(strict_types=1);

namespace App\Service\Admin\CustomField;

use App\Entity\CustomField;
use App\Entity\CustomFieldOption;
use App\Enum\SortConfig;
use App\Form\Admin\CustomFieldOptionCopyDto;
use App\Helper\ArrayHelper;
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
        $sort = SortConfig::START_REORDER_FROM;
        if ($targetCustomField->getCustomFieldOptions()->count()) {
            /** @var CustomFieldOption $customFieldOption */
            $customFieldOption = $targetCustomField->getCustomFieldOptions()->last();
            $sort = $customFieldOption->getSort();
        }

        $customFieldOptions = $customFieldOptionCopyDto->getSourceCustomFieldNotNull()->getCustomFieldOptions();
        foreach ($customFieldOptions as $sourceCustomFieldOption) {
            ++$sort;
            $newValue = $sourceCustomFieldOption->getValue();
            if (isset($currentOptions[$newValue])) {
                continue;
            }

            $newCustomFieldOption = clone $sourceCustomFieldOption;
            $newCustomFieldOption->setCustomField($targetCustomField);
            $newCustomFieldOption->setSort($sort);
            $targetCustomField->addCustomFieldOption($newCustomFieldOption);
            $this->em->persist($newCustomFieldOption);
        }
    }

    /**
     * @return array<int|string,CustomFieldOption>
     */
    private function getOptionsIndexedByValue(CustomField $targetCustomField): array
    {
        return ArrayHelper::indexBy(
            $targetCustomField->getCustomFieldOptions()->toArray(),
            static function (CustomFieldOption $customFieldOption) {
                return [$customFieldOption->getValueNotNull() => $customFieldOption];
            }
        );
    }
}
