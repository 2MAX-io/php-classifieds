<?php

declare(strict_types=1);

namespace App\Form\Admin;

use App\Entity\Listing;
use App\Form\Type\CategoryType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminListingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title', TextType::class, [
            'label' => 'trans.Title'
        ]);
        $builder->add('description', TextareaType::class, [
            'label' => 'trans.Description',
            'attr' => [
                'class' => 'form-listing-description-textarea'
            ]
        ]);
        $builder->add('category', CategoryType::class);
        $builder->add('phone', TextType::class, [
            'label' => 'trans.Phone'
        ]);
        $builder->add('email', EmailType::class, [
            'label' => 'trans.Email'
        ]);
        $builder->add('price', IntegerType::class, [
            'label' => 'trans.Price'
        ]);
        $builder->add('city', TextType::class, [
            'label' => 'trans.City'
        ]);
        $builder->add('customFields', HiddenType::class, [
            'mapped' => false,
            'attr' => [
                'class' => 'formCustomFieldsHidden'
            ]
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
