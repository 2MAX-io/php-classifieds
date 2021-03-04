<?php

declare(strict_types=1);

namespace App\Form\Admin\Settings;

use App\Form\Admin\Settings\Base\SettingTypeInterface;
use App\Form\Type\BoolRequiredType;
use App\Form\Type\CountyIsoType;
use App\Form\Type\LanguageIsoType;
use App\Service\Setting\SettingsDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimezoneType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

class SystemSettingsType extends AbstractType implements SettingTypeInterface
{
    /**
     * @var TranslatorInterface
     */
    private $trans;

    public function __construct(TranslatorInterface $trans)
    {
        $this->trans = $trans;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('languageIso', LanguageIsoType::class, [
            'label' => 'trans.Language',
            'required' => true,
            'constraints' => [
                new NotBlank(),
            ],
        ]);
        $builder->add('countryIso', CountyIsoType::class, [
            'label' => 'trans.Country',
            'required' => true,
            'constraints' => [
                new NotBlank(),
            ],
        ]);
        $builder->add('timezone', TimezoneType::class, [
            'label' => 'trans.Timezone',
            'required' => true,
            'empty_data' => false,
            'constraints' => [
                new NotBlank(),
            ],
        ]);
        $builder->add('dateFormat', TextType::class, [
            'label' => 'trans.Date format',
            'required' => true,
            'empty_data' => '',
            'constraints' => [
                new NotBlank(),
            ],
        ]);
        $builder->add('dateFormatShort', TextType::class, [
            'label' => 'trans.Date format short',
            'required' => true,
            'empty_data' => '',
            'constraints' => [
                new NotBlank(),
            ],
        ]);
        $builder->add('decimalSeparator', ChoiceType::class, [
            'label' => 'trans.Decimal separator in numbers',
            'required' => true,
            'choice_translation_domain' => false,
            'choices' => [
                '.' => '.',
                ',' => ',',
            ],
            'constraints' => [
                new NotBlank(),
            ],
        ]);
        $builder->add('thousandSeparator', ChoiceType::class, [
            'label' => 'trans.Thousand separator in numbers',
            'required' => true,
            'choice_translation_domain' => false,
            'choices' => [
                $this->trans->trans('trans.space') => ' ',
                ',' => ',',
                '.' => '.',
                $this->trans->trans('trans.none') => '',
            ],
            'constraints' => [
                new NotBlank(),
            ],
        ]);
        $builder->add('deleteExpiredListingFilesEnabled', BoolRequiredType::class, [
            'label' => 'trans.Delete expired listings files',
            'required' => true,
            'empty_data' => '',
            'constraints' => [
                new NotBlank(),
            ],
        ]);
        $builder->add('deleteExpiredListingFilesOlderThanDays', IntegerType::class, [
            'label' => 'trans.Delete expired listings files older than days',
            'required' => true,
            'empty_data' => false,
            'constraints' => [
                new NotBlank(),
                new GreaterThanOrEqual(['value' => 1]),
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
                    'mode' => Email::VALIDATION_MODE_STRICT,
                ]),
            ],
        ]);
        $builder->add('emailFromName', TextType::class, [
            'label' => 'trans.Name used before email address, in from field of email message',
            'required' => true,
            'empty_data' => '',
            'constraints' => [
                new NotBlank(),
                new Length(['min' => 4, 'max' => 40]),
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
                    'mode' => Email::VALIDATION_MODE_STRICT,
                ]),
            ],
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
