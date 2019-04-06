<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Listing;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ListingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('description', TextareaType::class)
            ->add(
                'category',
                EntityType::class,
                [
                    'choice_label' => 'name', 'class' => Category::class,
                    'placeholder' => 'trans.Select category',
                ]
            )
            ->add('price')
            ->add('phone')
            ->add('email')
            ->add('city');
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
