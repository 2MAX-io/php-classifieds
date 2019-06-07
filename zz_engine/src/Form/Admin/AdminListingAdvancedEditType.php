<?php

declare(strict_types=1);

namespace App\Form\Admin;

use App\Entity\Listing;
use App\Form\Type\AppDateTimeType;
use App\Form\Type\BoolRequiredType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class AdminListingAdvancedEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('featured', BoolRequiredType::class, [
            'label' => 'trans.Featured listing?',
            'constraints' => [
                new NotBlank(),
            ],
        ]);
        $builder->add('featuredUntilDate', AppDateTimeType::class, [
            'required' => false,
            'label' => 'trans.Featured until',
        ]);
        $builder->add('featuredWeight', IntegerType::class, [
            'label' => 'trans.Featured weight',
            'constraints' => [
                new NotBlank(),
            ],
        ]);
        $builder->add('orderByDate', AppDateTimeType::class, [
            'label' => 'trans.Date of last raising',
            'constraints' => [
                new NotBlank(),
            ],
        ]);
        $builder->add('validUntilDate', AppDateTimeType::class, [
            'label' => 'trans.Valid until',
            'constraints' => [
                new NotBlank(),
            ],
        ]);
        $builder->add('firstCreatedDate', AppDateTimeType::class, [
            'label' => 'trans.Date added',
            'constraints' => [
                new NotBlank(),
            ],
        ]);
        $builder->add('lastEditDate', AppDateTimeType::class, [
            'label' => 'trans.Last edit',
            'constraints' => [
                new NotBlank(),
            ],
        ]);
        $builder->add('adminLastActivationDate', AppDateTimeType::class, [
            'required' => false,
            'label' => 'trans.Admin last activation',
        ]);
        $builder->add('adminActivated', BoolRequiredType::class, [
            'label' => 'trans.Activated by Admin?',
            'constraints' => [
                new NotBlank(),
            ],
        ]);
        $builder->add('adminRejected', BoolRequiredType::class, [
            'label' => 'trans.Rejected by Admin?',
            'constraints' => [
                new NotBlank(),
            ],
        ]);
        $builder->add('rejectionReason', TextType::class, [
            'required' => false,
            'label' => 'trans.Reject reason (optional)'
        ]);
        $builder->add('adminRemoved', BoolRequiredType::class, [
            'label' => 'trans.Is listing removed by Admin?',
            'constraints' => [
                new NotBlank(),
            ],
        ]);
        $builder->add('userRemoved', BoolRequiredType::class, [
            'label' => 'trans.Is listing removed by user?',
            'constraints' => [
                new NotBlank(),
            ],
        ]);
        $builder->add('userDeactivated', BoolRequiredType::class, [
            'label' => 'trans.Is listing deactivated by user?',
            'constraints' => [
                new NotBlank(),
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => Listing::class,
            ]
        );
    }
}
