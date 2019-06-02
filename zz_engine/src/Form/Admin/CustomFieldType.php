<?php

declare(strict_types=1);

namespace App\Form\Admin;

use App\Entity\CustomField;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class CustomFieldType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('name', TextType::class, [
            'label' => 'trans.Name',
            'constraints' => [
                new NotBlank(),
            ],
        ]);
        $builder->add('nameForAdmin', TextType::class, [
            'label' => 'trans.Name for Admin only',
            'help' => 'trans.optional, hint for Admin, when having many custom fields with same name, not visible for users',
            'required' => false,
        ]);
        $builder->add('type', ChoiceType::class, [
            'label' => 'trans.Type',
            'choices' => [
                'trans.customFieldType.select_single' => CustomField::TYPE_SELECT_SINGLE,
                'trans.customFieldType.select' => CustomField::TYPE_SELECT,
                'trans.customFieldType.checkbox_multiple' => CustomField::TYPE_CHECKBOX_MULTIPLE,
                'trans.customFieldType.integer_range' => CustomField::TYPE_INTEGER_RANGE,
                'trans.customFieldType.year_range' => CustomField::TYPE_YEAR_RANGE,
            ],
            'constraints' => [
                new NotBlank(),
            ],
        ]);
        $builder->add('required', CheckboxType::class, [
            'label' => 'trans.Required?',
            'required' => false,
        ]);
        $builder->add('searchable', CheckboxType::class, [
            'label' => 'trans.Searchable?',
            'required' => false,
        ]);
        $builder->add('unit', TextType::class, [
            'label' => 'trans.Unit',
            'required' => false,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CustomField::class,
        ]);
    }
}
