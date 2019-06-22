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
                'trans.customFieldType.SELECT_SINGLE' => CustomField::SELECT_SINGLE,
                'trans.customFieldType.SELECT_AS_CHECKBOXES' => CustomField::SELECT_AS_CHECKBOXES,
                'trans.customFieldType.CHECKBOX_MULTIPLE' => CustomField::CHECKBOX_MULTIPLE,
                'trans.customFieldType.INTEGER_RANGE' => CustomField::INTEGER_RANGE,
                'trans.customFieldType.YEAR_RANGE' => CustomField::YEAR_RANGE,
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
