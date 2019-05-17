<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\CustomField;
use App\Entity\Listing;
use App\Service\Listing\CustomField\CustomFieldsForListingFormService;
use Minwork\Helper\Arr;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ListingCustomFieldListType extends AbstractType
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var CustomFieldsForListingFormService
     */
    private $customFieldsForListingFormService;

    public function __construct(RequestStack $requestStack, CustomFieldsForListingFormService $customFieldsForListingFormService)
    {
        $this->requestStack = $requestStack;
        $this->customFieldsForListingFormService = $customFieldsForListingFormService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $listing = $this->getListingEntity($options);
        $listingId = $listing ? $listing->getId() : null;
        $request = $this->requestStack->getMasterRequest();
        $customFieldList = Arr::getNestedElement($request->request->all(), ['listing', 'customFieldList']) ?? [];

        foreach ($this->customFieldsForListingFormService->getFields($listing->getCategory()->getId(), $listingId) as $customField) {


            $value = $customField->getListingCustomFieldValues()->first() ? $customField->getListingCustomFieldValues()->first()->getValue() : null;

            if (\in_array($customField->getType(), [CustomField::TYPE_SELECT_SINGLE, CustomField::TYPE_SELECT])) {
                $builder->add($customField->getId(), ChoiceType::class, [
                    'label' => $customField->getName(),
                    'placeholder' => 'trans.Select',
                    'choices' => $this->getChoices($customField),
                    'empty_data' => $value,
                    'constraints' => $this->getConstraints($customField),
                ]);
            }

            if (\in_array($customField->getType(), [CustomField::TYPE_CHECKBOX_MULTIPLE])) {
                $builder->add($customField->getId(), ChoiceType::class, [
                    'label' => $customField->getName(),
                    'placeholder' => 'trans.Select',
                    'expanded' => true,
                    'multiple' => true,
                    'choices' => $this->getChoices($customField),
//                'empty_data' => $value,
                    'constraints' => $this->getConstraints($customField),
                ]);
            }

            if (\in_array($customField->getType(), [CustomField::TYPE_INTEGER_RANGE, CustomField::TYPE_YEAR_RANGE])) {
                $builder->add($customField->getId(), IntegerType::class, [
                    'label' => $customField->getName(),
//                'empty_data' => $value,
                    'constraints' => $this->getConstraints($customField),
                ]);
            }


        }

//        foreach ($customFieldList as $requestCustomFieldId => $requestCustomFieldVal) {
//            $builder->add($requestCustomFieldId, TextType::class, [
//                'label' => 'trans.zz',
//                'empty_data' => '',
//                'constraints' => [
////                new NotBlank(),
////                new Length(['min' => 999999]),
//                ],
//            ]);
//        }

//        $builder->add('zzz1', TextType::class, [
//            'label' => 'trans.zz',
//            'empty_data' => '',
//            'constraints' => [
////                new NotBlank(),
////                new Length(['min' => 5]),
//            ],
//        ]);
//        $builder->add('zzz2', ChoiceType::class, [
//            'label' => 'trans.check',
//            'multiple' => true,
//            'expanded' => true,
//            'choices' => [
//                'asdf' => 1,
//            ],
//            'constraints' => [
////                new NotBlank(),
//            ],
//        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'allow_extra_fields' => true, // todo: remove
                'data_class' => null,
                'required' => false,
                'listingEntity' => null,
                'label' => false,
            ]
        );
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
}
