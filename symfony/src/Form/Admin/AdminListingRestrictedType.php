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
        $builder->add('featured', BoolType::class, [
            'label' => 'trans.Featured listing?'
        ]);
        $builder->add('featuredUntilDate', DateTimeType::class, [
            'label' => 'trans.Featured until'
        ]);
        $builder->add('featuredWeight', IntegerType::class, [
            'label' => 'trans.Featured weight'
        ]);
        $builder->add('orderByDate', DateTimeType::class, [
            'label' => 'trans.Date of last raising'
        ]);
        $builder->add('validUntilDate', DateTimeType::class, [
            'label' => 'trans.Valid until'
        ]);
        $builder->add('adminConfirmed', BoolType::class, [
            'label' => 'trans.Confirmed by Admin?'
        ]);
        $builder->add('adminRejected', BoolType::class, [
            'label' => 'trans.Rejected by Admin?'
        ]);
        $builder->add('rejectionReason', TextType::class, [
            'label' => 'trans.Reject reason (optional)'
        ]);
        $builder->add('adminRemoved', BoolType::class, [
            'label' => 'trans.Is listing removed by Admin?'
        ]);
        $builder->add('userRemoved', BoolType::class, [
            'label' => 'trans.Is listing removed by user?'
        ]);
        $builder->add('userDeactivated', BoolType::class, [
            'label' => 'trans.Is listing deactivated by user?'
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
