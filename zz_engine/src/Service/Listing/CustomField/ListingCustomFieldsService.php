<?php

declare(strict_types=1);

namespace App\Service\Listing\CustomField;

use App\Entity\Category;
use App\Entity\CustomField;
use App\Entity\Listing;
use App\Entity\ListingCustomFieldValue;
use App\Form\ListingCustomFieldsType;
use App\Helper\StringHelper;
use App\Repository\CustomFieldOptionRepository;
use App\Service\Listing\CustomField\Dto\CustomFieldFromRequestDto;
use App\Service\Listing\Save\Dto\ListingSaveDto;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\Join;
use Psr\Log\LoggerInterface;

class ListingCustomFieldsService
{
    /**
     * @var CustomFieldOptionRepository
     */
    private $customFieldOptionRepository;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        CustomFieldOptionRepository $customFieldOptionRepository,
        EntityManagerInterface $em,
        LoggerInterface $logger
    ) {
        $this->customFieldOptionRepository = $customFieldOptionRepository;
        $this->em = $em;
        $this->logger = $logger;
    }

    /**
     * @return CustomField[]
     */
    public function getCustomFields(Category $category, Listing $listing): array
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('customField');
        $qb->from(CustomField::class, 'customField');
        $qb->addSelect('customFieldOption');
        $qb->addSelect('customFieldForCategory');
        $qb->addSelect('category');
        $qb->addSelect('listingCustomFieldValue');
        $qb->join('customField.customFieldForCategories', 'customFieldForCategory');
        $qb->join('customFieldForCategory.category', 'category');
        $qb->leftJoin('customField.customFieldOptions', 'customFieldOption');

        $qb->leftJoin(
            'customField.listingCustomFieldValues',
            'listingCustomFieldValue',
            Join::WITH,
            (string) $qb->expr()->eq('listingCustomFieldValue.listing', ':listing'),
        );
        $qb->setParameter(':listing', $listing->getId());

        $qb->andWhere($qb->expr()->eq('category.id', ':category'));
        $qb->setParameter(':category', $category->getId());

        $qb->orderBy('customFieldForCategory.sort', Criteria::ASC);

        return $qb->getQuery()->getResult();
    }

    public function saveCustomFieldsToListing(ListingSaveDto $listingSaveDto): void
    {
        $listing = $listingSaveDto->getListing();
        $listingCustomFieldValues = $listing->getListingCustomFieldValues()->toArray();
        $listingSaveDto->setCurrentValueEntities($listingCustomFieldValues);
        $listingSaveDto->setCustomFieldValueEntitiesToRemove($listingCustomFieldValues);

        foreach ($listingSaveDto->getCustomFieldValuesFromRequest() as $customFieldId => $customFieldValueFromRequest) {
            if (\is_array($customFieldValueFromRequest)) {
                foreach ($customFieldValueFromRequest as $customFieldValue) {
                    if (StringHelper::emptyTrim($customFieldValue)) {
                        continue;
                    }

                    $customFieldFromRequestDto = new CustomFieldFromRequestDto();
                    $customFieldFromRequestDto->setCustomFieldId($customFieldId);
                    $customFieldFromRequestDto->setCustomFieldValueString($customFieldValue);
                    $listingCustomFieldValue = $this->saveCustomField(
                        $customFieldFromRequestDto,
                        $listingSaveDto,
                    );
                    $listing->addListingCustomFieldValue($listingCustomFieldValue);
                }

                continue;
            }

            $customFieldValue = $customFieldValueFromRequest;
            if (StringHelper::emptyTrim($customFieldValue)) {
                continue;
            }

            $customFieldFromRequestDto = new CustomFieldFromRequestDto();
            $customFieldFromRequestDto->setCustomFieldId($customFieldId);
            $customFieldFromRequestDto->setCustomFieldValueString($customFieldValue);
            $listingCustomFieldValue = $this->saveCustomField(
                $customFieldFromRequestDto,
                $listingSaveDto,
            );
            $listing->addListingCustomFieldValue($listingCustomFieldValue);
        }

        foreach ($listingSaveDto->getCustomFieldValueEntitiesToRemove() as $valueEntityToRemove) {
            $this->em->remove($valueEntityToRemove);
        }
    }

    public function saveCustomField(
        CustomFieldFromRequestDto $customFieldFromRequestDto,
        ListingSaveDto $listingSaveDto
    ): ListingCustomFieldValue {
        $customFieldValueString = $customFieldFromRequestDto->getCustomFieldValueString();
        $option = null;
        if (StringHelper::beginsWith(
            $customFieldValueString,
            ListingCustomFieldsType::CUSTOM_FIELD_OPTION_ID_PREFIX
        )) {
            $optionId = (int) \str_replace(
                ListingCustomFieldsType::CUSTOM_FIELD_OPTION_ID_PREFIX,
                '',
                $customFieldValueString,
            );
            $option = $this->customFieldOptionRepository->find($optionId);
            if (null === $option || null === $option->getValue()) {
                $this->logger->error('option should not be null', ['optionId' => $optionId]);
            }

            $customFieldValueString = $option->getValue() ?? (string) $optionId; // should not be null
        }

        $listingCustomFieldValue = $listingSaveDto->getCurrentCustomFieldValueEntity(
            $customFieldFromRequestDto,
            $option
        );
        if (!$listingCustomFieldValue) {
            $listingCustomFieldValue = new ListingCustomFieldValue();
        }

        /** @var CustomField $customField */
        $customField = $this->em->getReference(
            CustomField::class,
            $customFieldFromRequestDto->getCustomFieldId()
        );
        $listingCustomFieldValue->setValue($customFieldValueString);
        $listingCustomFieldValue->setCustomFieldOption($option);
        $listingCustomFieldValue->setCustomField($customField);
        $this->em->persist($listingCustomFieldValue);
        $listingSaveDto->stopCustomFieldValueRemove($listingCustomFieldValue);

        return $listingCustomFieldValue;
    }
}
