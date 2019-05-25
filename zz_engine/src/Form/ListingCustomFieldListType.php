<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Category;
use App\Entity\CustomField;
use App\Entity\Listing;
use App\Entity\ListingCustomFieldValue;
use App\Form\Type\YearType;
use App\Repository\CategoryRepository;
use App\Service\Listing\CustomField\CustomFieldsForListingFormService;
use Minwork\Helper\Arr;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

class ListingCustomFieldListType extends AbstractType
{
    public const CUSTOM_FIELD_LIST_FIELD = 'customFieldList';

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var CustomFieldsForListingFormService
     */
    private $customFieldsForListingFormService;

    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    /**
     * @var TranslatorInterface
     */
    private $trans;

    public function __construct(
        RequestStack $requestStack,
        CustomFieldsForListingFormService $customFieldsForListingFormService,
        CategoryRepository $categoryRepository,
        TranslatorInterface $trans
    ) {
        $this->requestStack = $requestStack;
        $this->customFieldsForListingFormService = $customFieldsForListingFormService;
        $this->categoryRepository = $categoryRepository;
        $this->trans = $trans;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $listing = $this->getListingEntity($options);
        $listingId = $listing ? $listing->getId() : null;
        $category = $this->getCategory($listing);
        if (!$category) {
            return;
        }

        foreach ($this->customFieldsForListingFormService->getFields($category->getId(), $listingId) as $customField) {

            if (\in_array($customField->getType(), [CustomField::TYPE_SELECT_SINGLE, CustomField::TYPE_SELECT])) {
                $choices = $this->getChoices($customField);
                if (!$choices) {
                    continue;
                }

                $builder->add($customField->getId(), ChoiceType::class, [
                    'label' => $customField->getName(),
                    'placeholder' => $this->trans->trans('trans.Select'),
                    'translation_domain' => false,
                    'choices' => $choices,
                    'data' => $this->getValue($customField),
                    'constraints' => $this->getConstraints($customField),
                    'required' => $customField->getRequired(),
                ]);
            }

            if (\in_array($customField->getType(), [CustomField::TYPE_CHECKBOX_MULTIPLE])) {
                $choices = $this->getChoices($customField);
                if (!$choices) {
                    continue;
                }
                $builder->add($customField->getId(), ChoiceType::class, [
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

            if (\in_array($customField->getType(), [CustomField::TYPE_INTEGER_RANGE])) {
                $builder->add($customField->getId(), IntegerType::class, [
                    'label' => $customField->getName(),
                    'translation_domain' => false,
                    'data' => $this->getValue($customField),
                    'constraints' => $this->getConstraints($customField),
                    'required' => $customField->getRequired(),
                ]);
            }

            if (\in_array($customField->getType(), [CustomField::TYPE_YEAR_RANGE])) {
                $builder->add($customField->getId(), YearType::class, [
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

    public function configureOptions(OptionsResolver $resolver)
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

    private function getListingEntity(array $options): ?Listing
    {
        return $options['listingEntity'] ?? null;
    }

    private function getChoices(CustomField $customField)
    {
        $return = [];
        foreach ($customField->getCustomFieldOptions() as $customFieldOption) {
            $return[$customFieldOption->getName()] = '__form_custom_field_option_id_' . $customFieldOption->getId();
        }

        return $return;
    }

    private function getConstraints(CustomField $customField): array
    {
        $constraints = [];

        if ($customField->getRequired()) {
            $constraints[] = new NotBlank();
        }

        return $constraints;
    }

    /**
     * @return array|string|null
     */
    private function getValue(CustomField $customField)
    {
        if (!$customField->getListingCustomFieldValueFirst()) {
            return null;
        }

        if (\in_array($customField->getType(), [CustomField::TYPE_SELECT_SINGLE, CustomField::TYPE_SELECT])) {
            return '__form_custom_field_option_id_' . $customField->getListingCustomFieldValueFirst()->getCustomFieldOption()->getId();
        }

        if (\in_array($customField->getType(), [CustomField::TYPE_INTEGER_RANGE, CustomField::TYPE_YEAR_RANGE])) {
            return $customField->getListingCustomFieldValueFirst()->getValue();
        }

        if (\in_array($customField->getType(), [CustomField::TYPE_CHECKBOX_MULTIPLE])) {
            return \array_map(function(ListingCustomFieldValue $customFieldValue) {
                return '__form_custom_field_option_id_' . $customFieldValue->getCustomFieldOption()->getId();
            }, $customField->getListingCustomFieldValues()->toArray());
        }

        throw new \UnexpectedValueException('custom field type not found');
    }

    private function getCategory(Listing $listing): ?Category
    {
        $post = $this->requestStack->getMasterRequest()->request->all();
        $categoryId = Arr::getNestedElement($post, [ListingType::LISTING_FIELD, ListingType::CATEGORY_FIELD]) ?? false;
        $category = $this->categoryRepository->find((int) $categoryId);

        if ($category) {
            return $category;
        }

        /**
         * must be after category from request, to handle cat change when editing
         */
        if ($listing->getCategory()) {
            return $listing->getCategory();
        }

        return null;
    }
}