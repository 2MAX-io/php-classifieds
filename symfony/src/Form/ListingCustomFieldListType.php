<?php

declare(strict_types=1);

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ListingCustomFieldListType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('zzz1', TextType::class, [
            'label' => 'trans.zz',
            'empty_data' => '',
            'constraints' => [
//                new NotBlank(),
//                new Length(['min' => 5]),
            ],
        ]);
        $builder->add('zzz2', ChoiceType::class, [
            'label' => 'trans.check',
            'multiple' => true,
            'expanded' => true,
            'choices' => [
                'asdf' => 1,
            ],
            'constraints' => [
//                new NotBlank(),
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'allow_extra_fields' => true, // todo: remove
                'data_class' => null,
                'required' => false,
            ]
        );
    }
}
