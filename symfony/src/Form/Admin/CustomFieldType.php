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
            'choices' => [

            ],
        ]);
        $builder->add('required', CheckboxType::class, [
            'label' => 'trans.Required?',
        ]);
        $builder->add('searchable', CheckboxType::class, [
            'label' => 'trans.Searchable?',
        ]);
        $builder->add('unit', TextType::class, [
            'label' => 'trans.Unit',
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CustomField::class,
        ]);
    }
}
