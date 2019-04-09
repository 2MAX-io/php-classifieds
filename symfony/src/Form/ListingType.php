<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Listing;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

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
            ->add('validityTimeDays', ChoiceType::class, [
                'mapped' => false,
                'placeholder' => 'trans.Chose validityTime',
                'choices' => $this->getValidityTimeDaysChoices(),
                'constraints' => [
                    new Constraints\Choice([
                        'choices' => $this->getValidityTimeDaysChoices()
                    ]),
                ],
            ])
            ->add('city')
            ->add('file', FileType::class, [
                'mapped' => false,
                'required' => false,
                'constraints' => [
//                    new Assert\File(['mimeTypes']) // todo: mime validation
                ]
            ])
        ;
    }

    private function getValidityTimeDaysChoices(): array
    {
        return [
            'trans.1 week' => 9,
            'trans.2 weeks' => 14,
            'trans.3 weeks' => 21,
            'trans.31 days' => 31,
        ];
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
