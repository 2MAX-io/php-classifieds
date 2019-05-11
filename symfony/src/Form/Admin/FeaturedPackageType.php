<?php

declare(strict_types=1);

namespace App\Form\Admin;

use App\Entity\FeaturedPackage;
use App\Form\Type\CustomMoneyType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

class FeaturedPackageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('adminName', TextType::class, [
            'label' => 'trans.Name for admin',
        ]);
        $builder->add('name', TextType::class, [
            'label' => 'trans.Name',
        ]);
        $builder->add('description', TextareaType::class, [
            'label' => 'trans.Description',
        ]);
        $builder->add('priceFloat', CustomMoneyType::class, [
            'label' => 'trans.Price',
            'constraints' => [
                new Constraints\GreaterThanOrEqual([
                    'value' => 0,
                ]),
            ],
        ]);
        $builder->add('daysFeaturedExpire', IntegerType::class, [
            'label' => 'trans.Days of featured',
        ]);
        $builder->add('daysListingExpire', IntegerType::class, [
            'label' => 'trans.Days of expiration',
        ]);
        $builder->add('defaultPackage', CheckboxType::class, [
            'label' => 'trans.Use when no package set for category, as default?',
            'required' => false,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => FeaturedPackage::class,
        ]);
    }
}