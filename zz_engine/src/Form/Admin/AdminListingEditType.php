<?php

declare(strict_types=1);

namespace App\Form\Admin;

use App\Entity\Listing;
use App\Form\ListingCustomFieldListType;
use App\Form\ListingType;
use App\Form\Type\BoolType;
use App\Form\Type\CategoryType;
use App\Form\Type\PriceForType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class AdminListingEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('title', TextType::class, [
            'label' => 'trans.Title',
            'required' => true,
            'empty_data' => '',
            'constraints' => [
                new NotBlank(),
            ],
            'attr'=> [
                'maxlength' => 70,
            ]
        ]);
        $builder->add('description', TextareaType::class, [
            'label' => 'trans.Description',
            'attr' => [
                'class' => 'form-listing-description-textarea'
            ],
            'required' => true,
            'empty_data' => '',
            'constraints' => [
                new NotBlank(),
                new Length(['min' => 1]),
            ],
        ]);
        $builder->add('category', CategoryType::class, [
            'constraints' => [
                new NotBlank(),
            ],
        ]);
        $builder->add('phone', TextType::class, [
            'label' => 'trans.Phone',
        ]);
        $builder->add('email', EmailType::class, [
            'label' => 'trans.Email',
        ]);
        $builder->add('emailShow', CheckboxType::class, [
            'label' => 'trans.Show email?',
        ]);
        $builder->add('price', IntegerType::class, [
            'label' => 'trans.Amount or price'
        ]);
        $builder->add('priceFor', PriceForType::class, [
            'required' => false,
        ]);
        $builder->add('priceNegotiable', BoolType::class, [
            'label' => 'trans.Amount is negotiable?',
        ]);
        $builder->add('city', TextType::class, [
            'label' => 'trans.City'
        ]);
        $builder->add(
            ListingCustomFieldListType::CUSTOM_FIELD_LIST_FIELD, ListingCustomFieldListType::class, [
            'listingEntity' => $options['data'],
            'mapped' => false,
            'attr' => [
                'class' => 'formCustomFieldList'
            ],
            'form_group_attr' => [
                'class' => 'mb-0',
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => Listing::class,
                'required' => false,
            ]
        );
    }

    public function getBlockPrefix(): string
    {
        return ListingType::LISTING_FIELD;
    }
}
