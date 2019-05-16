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
use Symfony\Component\Validator\Constraints\NotBlank;

class AdminListingAdvancedEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('featured', BoolType::class, [
            'label' => 'trans.Featured listing?',
            'constraints' => [
                new NotBlank(),
            ],
        ]);
        $builder->add('featuredUntilDate', DateTimeType::class, [
            'required' => false,
            'label' => 'trans.Featured until',
        ]);
        $builder->add('featuredWeight', IntegerType::class, [
            'label' => 'trans.Featured weight',
            'constraints' => [
                new NotBlank(),
            ],
        ]);
        $builder->add('orderByDate', DateTimeType::class, [
            'label' => 'trans.Date of last raising',
            'constraints' => [
                new NotBlank(),
            ],
        ]);
        $builder->add('validUntilDate', DateTimeType::class, [
            'label' => 'trans.Valid until',
            'constraints' => [
                new NotBlank(),
            ],
        ]);
        $builder->add('adminActivated', BoolType::class, [
            'label' => 'trans.Activated by Admin?',
            'constraints' => [
                new NotBlank(),
            ],
        ]);
        $builder->add('adminRejected', BoolType::class, [
            'label' => 'trans.Rejected by Admin?',
            'constraints' => [
                new NotBlank(),
            ],
        ]);
        $builder->add('rejectionReason', TextType::class, [
            'required' => false,
            'label' => 'trans.Reject reason (optional)'
        ]);
        $builder->add('adminRemoved', BoolType::class, [
            'label' => 'trans.Is listing removed by Admin?',
            'constraints' => [
                new NotBlank(),
            ],
        ]);
        $builder->add('userRemoved', BoolType::class, [
            'label' => 'trans.Is listing removed by user?',
            'constraints' => [
                new NotBlank(),
            ],
        ]);
        $builder->add('userDeactivated', BoolType::class, [
            'label' => 'trans.Is listing deactivated by user?',
            'constraints' => [
                new NotBlank(),
            ],
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
