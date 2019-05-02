<?php

declare(strict_types=1);

namespace App\Form\Admin;

use App\Form\Type\LanguageTwoLettersType;
use App\Form\Type\PageType;
use App\Service\Setting\SettingsDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class SettingsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('indexPageTitle', TextType::class, [
            'label' => 'trans.Home page title',
            'required' => true,
            'empty_data' => '',
            'constraints' => [
                new NotBlank(),
                new Length(['min' => 1]),
            ],
        ]);
        $builder->add('pageTitleSuffix', TextType::class, [
            'label' => 'trans.Page title suffix',
            'required' => true,
            'empty_data' => '',
            'constraints' => [
                new NotBlank(),
                new Length(['min' => 1]),
            ],
        ]);
        $builder->add('metaDescription', TextareaType::class, [
            'label' => 'trans.Meta description',
            'empty_data' => '',
            'constraints' => [
                new Length(['min' => 2]),
            ],
        ]);
        $builder->add('metaKeywords', TextareaType::class, [
            'label' => 'trans.Meta keywords',
            'empty_data' => '',
            'constraints' => [
                new NotBlank(),
                new Length(['min' => 5]),
            ],
        ]);
        $builder->add('rssTitle', TextType::class, [
            'label' => 'trans.RSS title',
            'required' => true,
            'empty_data' => '',
            'constraints' => [
                new NotBlank(),
                new Length(['min' => 2]),
            ],
        ]);
        $builder->add('rssDescription', TextareaType::class, [
            'label' => 'trans.RSS description',
            'required' => true,
            'empty_data' => '',
            'constraints' => [
                new NotBlank(),
                new Length(['min' => 2]),
            ],
        ]);
        $builder->add('linkTermsConditions', PageType::class, [
            'label' => 'trans.Link to terms and conditions',
            'required' => true,
            'constraints' => [
                new NotBlank(),
            ],
        ]);
        $builder->add('languageTwoLetters', LanguageTwoLettersType::class, [
            'label' => 'trans.Language',
            'required' => true,
            'constraints' => [
                new NotBlank(),
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => SettingsDto::class,
                'required' => false,
            ]
        );
    }
}
