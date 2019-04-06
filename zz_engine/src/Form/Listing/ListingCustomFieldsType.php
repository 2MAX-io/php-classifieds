<?php

declare(strict_types=1);

namespace App\Form\Listing;

use App\Entity\Category;
use App\Entity\CustomField;
use App\Entity\Listing;
use App\Entity\ListingCustomFieldValue;
use App\Form\Type\YearType;
use App\Helper\ArrayHelper;
use App\Repository\CategoryRepository;
use App\Service\Listing\CustomField\ListingCustomFieldsService;
use Minwork\Helper\Arr;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

class ListingCustomFieldsType extends AbstractType
{
    public const CUSTOM_FIELD_LIST_FIELD = 'customFieldList';
    public const CUSTOM_FIELD_OPTION_ID_PREFIX = '__custom_field_option_id_';

    /**
     * @var ListingCustomFieldsService
     */
    private $listingCustomFieldsService;

    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var TranslatorInterface
     */
    private $trans;

    public function __construct(
        ListingCustomFieldsService $listingCustomFieldsService,
        CategoryRepository $categoryRepository,
        RequestStack $requestStack,
        TranslatorInterface $trans
    ) {
        $this->requestStack = $requestStack;
        $this->listingCustomFieldsService = $listingCustomFieldsService;
        $this->categoryRepository = $categoryRepository;
        $this->trans = $trans;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $listing = $this->getListingEntity($options);
        if (!$listing) {
            return;
        }
        $category = $this->getCategory($listing);
        if (!$category) {
            return;
        }

        $customFields = $this->listingCustomFieldsService->getCustomFields($category, $listing);
        foreach ($customFields as $customField) {
            $isSelectOfAnyType = ArrayHelper::inArray($customField->getType(), [
                CustomField::SELECT_SINGLE,
                CustomField::SELECT_AS_CHECKBOXES,
            ]);
            $fieldName = (string) $customField->getId();
            if ($isSelectOfAnyType) {
                $choices = $this->getChoices($customField);
                if (!$choices) {
                    continue;
                }

                $builder->add($fieldName, ChoiceType::class, [
                    'label' => $customField->getName(),
                    'placeholder' => $this->trans->trans('trans.Select'),
                    'translation_domain' => false,
                    'choices' => $choices,
                    'data' => $this->getValue($customField),
                    'constraints' => $this->getConstraints($customField),
                    'required' => $customField->getRequired(),
                ]);
            }

            if (CustomField::CHECKBOX_MULTIPLE === $customField->getType()) {
                $choices = $this->getChoices($customField);
                if (!$choices) {
                    continue;
                }
                $builder->add($fieldName, ChoiceType::class, [
                    'label' => $customField->getName(),
                    'placeholder' => $this->trans->trans('trans.Select'),
                    'expanded' => true,
                    'multiple' => true,
                    'translation_domain' => false,
                    'choices' => $choices,
                    'data' => $this->getValue($customField),
                    'constraints' => $this->getConstraints($customField),
                    'required' => false,
                ]);
            }

            if (CustomField::INTEGER_RANGE === $customField->getType()) {
                $maxIntegerValue = (int) \str_repeat('9', 12);
                $label = $customField->getName();
                if ($customField->getUnit()) {
                    $label = $customField->getName().' ['.$customField->getUnit().']';
                }
                $builder->add($fieldName, IntegerType::class, [
                    'label' => $label,
                    'translation_domain' => false,
                    'data' => $this->getValue($customField),
                    'constraints' => \array_merge(
                        $this->getConstraints($customField),
                        [
                            new GreaterThanOrEqual(['value' => 0]),
                            new LessThanOrEqual(['value' => $maxIntegerValue]),
                        ],
                    ),
                    'required' => $customField->getRequired(),
                    'attr' => [
                        'min' => 0,
                        'max' => $maxIntegerValue,
                    ],
                ]);
            }

            if (CustomField::YEAR_RANGE === $customField->getType()) {
                $builder->add($fieldName, YearType::class, [
                    'label' => $customField->getName(),
                    'placeholder' => $this->trans->trans('trans.Select'),
                    'translation_domain' => false,
                    'data' => $this->getValue($customField),
                    'constraints' => $this->getConstraints($customField),
                    'required' => $customField->getRequired(),
                ]);
            }
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => null,
                'listingEntity' => null,
                'label' => false,
            ]
        );
    }

    public function getBlockPrefix(): string
    {
        return static::CUSTOM_FIELD_LIST_FIELD;
    }

    /**
     * @param array<string,mixed> $options
     */
    private function getListingEntity(array $options): ?Listing
    {
        return $options['listingEntity'] ?? null;
    }

    /**
     * @return array<string,int|string>
     */
    private function getChoices(CustomField $customField): array
    {
        $choices = [];
        foreach ($customField->getCustomFieldOptions() as $customFieldOption) {
            $choices[$customFieldOption->getNameNotNull()] = static::CUSTOM_FIELD_OPTION_ID_PREFIX.$customFieldOption->getIdNotNull();
        }

        return $choices;
    }

    /**
     * @return Constraint[]
     */
    private function getConstraints(CustomField $customField): array
    {
        $constraints = [];
        if ($customField->getRequired()) {
            $constraints[] = new NotBlank();
        }

        return $constraints;
    }

    /**
     * @return array<int,string|null>|string|null
     */
    private function getValue(CustomField $customField)
    {
        if (!$customField->getListingCustomFieldValueFirst()) {
            return null;
        }

        $isSelectOfAnyKind = ArrayHelper::inArray(
            $customField->getType(),
            [CustomField::SELECT_SINGLE, CustomField::SELECT_AS_CHECKBOXES],
        );
        if ($isSelectOfAnyKind) {
            $customFieldOption = $customField->getListingCustomFieldValueFirst()->getCustomFieldOption();
            if (null === $customFieldOption) {
                return null;
            }

            return self::CUSTOM_FIELD_OPTION_ID_PREFIX.$customFieldOption->getIdNotNull();
        }

        if (ArrayHelper::inArray(
            $customField->getType(),
            [CustomField::INTEGER_RANGE, CustomField::YEAR_RANGE]
        )) {
            $value = $customField->getListingCustomFieldValueFirst()->getValue();
            if (!\is_numeric($value)) {
                return null;
            }

            return $value;
        }

        if (CustomField::CHECKBOX_MULTIPLE === $customField->getType()) {
            return \array_map(static function (ListingCustomFieldValue $customFieldValue) {
                $customFieldOption = $customFieldValue->getCustomFieldOption();
                if (null === $customFieldOption) {
                    return null;
                }

                return static::CUSTOM_FIELD_OPTION_ID_PREFIX.$customFieldOption->getIdNotNull();
            }, $customField->getListingCustomFieldValues()->toArray());
        }

        throw new \UnexpectedValueException("custom field type: `{$customField->getType()}` not found");
    }

    private function getCategory(Listing $listing): ?Category
    {
        $requestData = $this->requestStack->getMasterRequest()->request->all();
        $categoryId = Arr::getNestedElement($requestData, [ListingType::LISTING_FIELD, ListingType::CATEGORY_FIELD]) ?? false;
        $category = $this->categoryRepository->find((int) $categoryId);
        if ($category) {
            return $category;
        }

        /*
         * must be after category from request, to handle cat change when editing
         */
        if ($listing->getCategory()) {
            return $listing->getCategory();
        }

        return null;
    }
}
