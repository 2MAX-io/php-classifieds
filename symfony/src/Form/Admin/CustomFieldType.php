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

class CustomFieldType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', TextType::class, [
            'label' => 'trans.Name',
        ]);
        $builder->add('type', ChoiceType::class, [
            'label' => 'trans.Type',
            'placeholder' => 'trans.Select',
            'choices' => [
                'trans.customFieldType.select' => CustomField::TYPE_SELECT,
                'trans.customFieldType.year_range' => CustomField::TYPE_INTEGER_RANGE,
                'trans.customFieldType.integer_range' => CustomField::TYPE_INTEGER_RANGE,
                'trans.customFieldType.checkbox_multiple' => CustomField::TYPE_CHECKBOX_MULTIPLE,
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

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CustomField::class,
        ]);
    }
}
