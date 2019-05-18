<?php

declare(strict_types=1);

namespace App\Form\Admin;

use App\Form\Type\BoolRequiredType;
use App\Form\Type\LanguageTwoLettersType;
use App\Form\Type\PageType;
use App\Service\Setting\SettingsDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\GreaterThan;
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
        $builder->add('footerSiteCopyright', TextType::class, [
            'label' => 'trans.Site copyright in footer',
            'help' => 'trans.%year% would be replaced to current year',
            'required' => true,
            'empty_data' => '',
            'constraints' => [
                new NotBlank(),
                new Length(['min' => 2]),
            ],
        ]);
        $builder->add('emailFromAddress', EmailType::class, [
            'label' => 'trans.Email address used in from field of email message',
            'help' => 'trans.Must match email from which you send messages',
            'required' => true,
            'empty_data' => '',
            'constraints' => [
                new NotBlank(),
                new Length(['min' => 4]),
                new Email([
                    'mode' => Email::VALIDATION_MODE_STRICT
                ]),
            ],
        ]);
        $builder->add('emailFromName', TextType::class, [
            'label' => 'trans.Name used before email address, in from field of email message',
            'required' => true,
            'empty_data' => '',
            'constraints' => [
                new NotBlank(),
                new Length(['min' => 4]),
            ],
        ]);
        $builder->add('emailReplyTo', EmailType::class, [
            'label' => 'trans.Email used when user replies to emails from this application',
            'required' => true,
            'empty_data' => '',
            'constraints' => [
                new NotBlank(),
                new Length(['min' => 4]),
                new Email([
                    'mode' => Email::VALIDATION_MODE_STRICT
                ]),
            ],
        ]);
        $builder->add('emailConfigUrl', TextType::class, [
            'label' => 'trans.Configuration url for sending emails',
            'help' => 'trans.examples: smtp://mail.host.com:465?encryption=ssl&auth_mode=plain&username=login@mail.com&password=password',
            'required' => true,
            'empty_data' => '',
            'constraints' => [
                new NotBlank(),
                new Length(['min' => 4]),
            ],
        ]);
        $builder->add('itemsPerPageMax', IntegerType::class, [
            'label' => 'trans.Maximum items per page',
            'required' => true,
            'empty_data' => '',
            'constraints' => [
                new NotBlank(),
                new GreaterThan(['value' => 0]),
            ],
        ]);
        $builder->add('requireListingAdminActivation', BoolRequiredType::class, [
            'label' => 'trans.Require listing activation by admin before making public',
            'required' => true,
            'constraints' => [
                new NotBlank(),
            ],
        ]);
        $builder->add('linkTermsConditions', PageType::class, [
            'label' => 'trans.Link to terms and conditions',
            'required' => true,
            'constraints' => [
                new NotBlank(),
            ],
        ]);
        $builder->add('linkPrivacyPolicy', PageType::class, [
            'label' => 'trans.Link to privacy policy',
            'required' => true,
            'constraints' => [
                new NotBlank(),
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
        $builder->add('currency', TextType::class, [
            'label' => 'trans.Currency',
            'required' => true,
            'constraints' => [
                new NotBlank(),
            ],
        ]);
        $builder->add('allowedCharacters', TextType::class, [
            'label' => 'trans.Allowed characters',
            'required' => true,
            'constraints' => [
                new NotBlank(),
            ],
        ]);
        $builder->add('wordsToRemoveFromTitle', TextareaType::class, [
            'label' => 'trans.Words to remove from listing title, separated by new line',
            'required' => true,
            'constraints' => [
            ],
        ]);
        $builder->add('searchPlaceholder', TextType::class, [
            'label' => 'trans.Search examples',
            'required' => true,
            'constraints' => [
                new NotBlank(),
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
                new NotBlank(),
            ],
        ]);
        $builder->add('masterSiteAnchorText', TextType::class, [
            'label' => 'trans.Text of link to master site',
            'required' => true,
            'constraints' => [
                new NotBlank(),
            ],
        ]);
        $builder->add('paymentGatewayPaymentDescription', TextType::class, [
            'label' => 'trans.Payment gateway - payment description',
            'help' => 'trans.used in payment gateway as description of what has been bought, ie on receipt',
            'required' => true,
            'constraints' => [
                new NotBlank(),
            ],
        ]);
        $builder->add('paymentPayPalMode', ChoiceType::class, [
            'label' => 'trans.Payments - PayPal mode',
            'required' => true,
            'placeholder' => 'trans.Select',
            'choices' => [
                'trans.paypal.sandbox' => 'sandbox',
                'trans.paypal.live' => 'live',
            ],
            'constraints' => [
                new NotBlank(),
            ],
        ]);
        $builder->add('paymentPayPalClientId', TextType::class, [
            'label' => 'trans.Payments - PayPal Client ID',
            'required' => true,
            'constraints' => [
                new NotBlank(),
            ],
        ]);
        $builder->add('paymentPayPalClientSecret', TextType::class, [
            'label' => 'trans.Payments - PayPal Client secret',
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
