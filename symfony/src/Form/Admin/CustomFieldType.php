<?php

declare(strict_types=1);

namespace App\Form\Admin;

use App\Entity\CustomField;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CustomFieldType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name');
        $builder->add('type');
        $builder->add('required');
        $builder->add('searchable');
        $builder->add('unit');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CustomField::class,
        ]);
    }
}
