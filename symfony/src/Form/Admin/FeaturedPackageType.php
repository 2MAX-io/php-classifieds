<?php

namespace App\Form\Admin;

use App\Entity\FeaturedPackage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FeaturedPackageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('adminName');
        $builder->add('name');
        $builder->add('description');
        $builder->add('price');
        $builder->add('daysFeaturedExpire');
        $builder->add('daysListingExpire');
        $builder->add('defaultPackage');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => FeaturedPackage::class,
        ]);
    }
}
