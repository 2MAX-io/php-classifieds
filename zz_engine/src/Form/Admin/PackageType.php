<?php

declare(strict_types=1);

namespace App\Form\Admin;

use App\Entity\Package;
use App\Form\Type\AppMoneyType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\Constraints\NotBlank;

class PackageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('name', TextType::class, [
            'label' => 'trans.Name',
            'constraints' => [
                new NotBlank(),
            ],
        ]);
        $builder->add('adminName', TextType::class, [
            'required' => false,
            'label' => 'trans.Name for admin',
        ]);
        $builder->add('description', TextareaType::class, [
            'label' => 'trans.Description',
            'required' => false,
            'attr' => [
                'class' => 'js__textareaAutosize',
            ],
        ]);
        $builder->add('priceFloat', AppMoneyType::class, [
            'label' => 'trans.Price',
            'required' => false,
            'constraints' => [
                new Constraints\GreaterThanOrEqual([
                    'value' => 0,
                ]),
            ],
        ]);
        $builder->add('daysListingExpire', IntegerType::class, [
            'label' => 'trans.Days of expiration',
            'constraints' => [
                new NotBlank(),
                new Constraints\GreaterThanOrEqual([
                    'value' => 0,
                ]),
                new Constraints\LessThanOrEqual([
                    'value' => 3650,
                ]),
            ],
        ]);
        $builder->add('daysFeaturedExpire', IntegerType::class, [
            'label' => 'trans.Days of featured',
            'constraints' => [
                new NotBlank(),
                new Constraints\GreaterThanOrEqual([
                    'value' => 0,
                ]),
                new Constraints\LessThanOrEqual([
                    'value' => 3650,
                ]),
            ],
        ]);
        $builder->add('secondsFeaturedExpire', IntegerType::class, [
            'label' => 'trans.Seconds of featured',
            'constraints' => [
                new NotBlank(),
                new Constraints\GreaterThanOrEqual([
                    'value' => 0,
                ]),
            ],
        ]);
        $builder->add('pullUpOlderThanDays', AppMoneyType::class, [
            'label' => 'trans.Pull up on listing expiration extend, when last pull up older than days',
            'required' => false,
            'constraints' => [
                new Constraints\GreaterThanOrEqual([
                    'value' => 0,
                ]),
            ],
        ]);
        $builder->add('listingFeaturedPriority', AppMoneyType::class, [
            'label' => 'trans.Listing featured priority',
            'constraints' => [
                new NotBlank(),
                new Constraints\GreaterThanOrEqual([
                    'value' => 0,
                ]),
            ],
        ]);
        $builder->add('defaultPackage', CheckboxType::class, [
            'label' => 'trans.Use when no package set for category, as default?',
            'required' => false,
        ]);
        $builder->add('demoPackage', CheckboxType::class, [
            'label' => 'trans.Demonstration of featured listing, user can use only once',
            'required' => false,
        ]);
        $builder->add('showPrice', CheckboxType::class, [
            'label' => 'trans.Show price?',
            'required' => false,
        ]);
        $builder->add('showFeaturedDays', CheckboxType::class, [
            'label' => 'trans.Show the number of featured days?',
            'required' => false,
        ]);
        $builder->add('showExpirationDays', CheckboxType::class, [
            'label' => 'trans.Show the days until expiration?',
            'required' => false,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Package::class,
        ]);
    }
}
