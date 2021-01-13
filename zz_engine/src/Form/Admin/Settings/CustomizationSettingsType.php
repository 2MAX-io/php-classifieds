<?php

declare(strict_types=1);

namespace App\Form\Admin\Settings;

use App\Form\Type\BoolRequiredType;
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
use Symfony\Component\Validator\Constraints\Url;

class CustomizationSettingsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
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
                new NotBlank(),
                new Length(['min' => 2, 'max' => 158]),
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
        $builder->add('footerSiteCopyright', TextType::class, [
            'label' => 'trans.Site copyright in footer',
            'help' => 'trans.%year% would be replaced to current year',
            'required' => true,
            'empty_data' => '',
            'constraints' => [
                new NotBlank(),
                new Length(['min' => 2]),
            ],
        ]);$builder->add('linkContact', PageType::class, [
        'label' => 'trans.Link to contact page',
        'required' => true,
        'constraints' => [
        ],
    ]);
        $builder->add('linkAdvertisement', PageType::class, [
            'label' => 'trans.Link to information about advertisement',
            'required' => true,
            'constraints' => [
            ],
        ]);
        $builder->add('linkTermsConditions', PageType::class, [
            'label' => 'trans.Link to terms and conditions',
            'required' => true,
            'constraints' => [
            ],
        ]);
        $builder->add('linkPrivacyPolicy', PageType::class, [
            'label' => 'trans.Link to privacy policy',
            'required' => true,
            'constraints' => [
            ],
        ]);
        $builder->add('linkRejectionReason', PageType::class, [
            'label' => 'trans.Link to rejection reasons',
            'required' => true,
            'placeholder' => 'trans.not required'
        ]);
        $builder->add('languageTwoLetters', LanguageTwoLettersType::class, [
            'label' => 'trans.Language',
            'required' => true,
            'constraints' => [
                new NotBlank(),
            ],
        ]);
        $builder->add('searchPlaceholder', TextType::class, [
            'label' => 'trans.Search examples',
            'required' => true,
            'constraints' => [
                new Length(['max' => 45]),
            ],
        ]);
        $builder->add('masterSiteLinkShow', BoolRequiredType::class, [
            'label' => 'trans.Show link to master site',
            'help' => 'trans.if this classifieds site, is module of main site, this option enables linking to your master site in breadcrumbs, navigation and admin',
            'required' => true,
            'constraints' => [
                new NotBlank(),
            ],
        ]);
        $builder->add('masterSiteUrl', TextType::class, [
            'label' => 'trans.Url to master site',
            'required' => true,
            'constraints' => [
                new Url(),
            ],
        ]);
        $builder->add('masterSiteAnchorText', TextType::class, [
            'label' => 'trans.Text of link to master site',
            'required' => true,
            'constraints' => [
                new Length(['min'=> 2]),
            ],
        ]);
        $builder->add('customJavascriptBottom', TextareaType::class, [
            'label' => 'trans.Custom javascript - bottom of page',
            'help' => 'trans.put your code inside: <script></script>',
            'required' => true,
        ]);
        $builder->add('customJavascriptInHead', TextareaType::class, [
            'label' => 'trans.Custom javascript - top of page in HEAD',
            'help' => 'trans.put your code inside: <script></script>',
            'required' => true,
        ]);
        $builder->add('customCss', TextareaType::class, [
            'label' => 'trans.Custom CSS',
            'help' => 'trans.put your code inside: <style type="text/css"></style>',
            'required' => true,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => SettingsDto::class,
                'required' => false,
            ]
        );
    }
}
