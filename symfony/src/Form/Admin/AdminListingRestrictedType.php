<?php

declare(strict_types=1);

namespace App\Form\Admin;

use App\Entity\Listing;
use App\Form\Type\BoolType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminListingRestrictedType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('premium', BoolType::class, [
//            'label' => 'trans.Title'
        ]);
        $builder->add('premiumUntilDate', DateTimeType::class, [
//            'label' => 'trans.Title'
        ]);
        $builder->add('premiumWeight', IntegerType::class, [
//            'label' => 'trans.Title'
        ]);
        $builder->add('lastReactivationDate', DateTimeType::class, [
//            'label' => 'trans.Title'
        ]);
        $builder->add('validUntilDate', DateTimeType::class, [
//            'label' => 'trans.Title'
        ]);
        $builder->add('adminConfirmed', BoolType::class, [
//            'label' => 'trans.Title'
        ]);
        $builder->add('adminRejected', BoolType::class, [
//            'label' => 'trans.Title'
        ]);
        $builder->add('rejectionReason', TextType::class, [
//            'label' => 'trans.Title'
        ]);
        $builder->add('userRemoved', BoolType::class, [
//            'label' => 'trans.Title'
        ]);
        $builder->add('userDeactivated', BoolType::class, [
//            'label' => 'trans.Title'
        ]);
        $builder->add('adminRemoved', BoolType::class, [
//            'label' => 'trans.Title'
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => Listing::class,
            ]
        );
    }
}
