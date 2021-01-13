<?php

declare(strict_types=1);

namespace App\Form\Admin\Settings;

use App\Form\Type\BoolRequiredType;
use App\Form\Type\BoolType;
use App\Form\Type\LanguageTwoLettersType;
use App\Form\Type\PageType;
use App\Service\Setting\SettingsDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
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
use Symfony\Component\Validator\Constraints\Url;

class SettingsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

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
                    'mode' => Email::VALIDATION_MODE_STRICT
                ]),
            ],
        ]);
        $builder->add('itemsPerPageMax', IntegerType::class, [
            'label' => 'trans.Items per page',
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
        $builder->add('allowedCharactersEnabled', CheckboxType::class, [
            'label' => 'trans.Remove non standard characters in listing - Enabled',
            'help' => 'trans.allows to remove all non standard characters from listing, except those which are white listed bellow',
            'required' => true,
            'constraints' => [],
        ]);
        $builder->add('allowedCharacters', TextType::class, [
            'label' => 'trans.Remove non standard characters in listing - List of allowed characters',
            'help' => 'trans.enter here, all characters specific to your language, except for standard characters A-Z, 0-9, for example: ąĄßöИ, if needed',
            'required' => true,
            'constraints' => [],
        ]);
        $builder->add('wordsToRemoveFromTitle', TextareaType::class, [
            'label' => 'trans.Words to remove from listing title, separated by new line',
            'required' => true,
            'constraints' => [
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
