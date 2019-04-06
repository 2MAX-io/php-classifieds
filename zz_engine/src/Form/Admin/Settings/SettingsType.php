<?php

declare(strict_types=1);

namespace App\Form\Admin\Settings;

use App\Form\Admin\Settings\Base\SettingTypeInterface;
use App\Form\Type\BoolRequiredType;
use App\Service\Setting\SettingsDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\NotBlank;

class SettingsType extends AbstractType implements SettingTypeInterface
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('requireListingAdminActivation', BoolRequiredType::class, [
            'label' => 'trans.Require listing activation by admin before publishing',
            'required' => true,
            'constraints' => [
                new NotBlank(),
            ],
        ]);
        $builder->add('itemsPerPage', IntegerType::class, [
            'label' => 'trans.Items per page',
            'required' => true,
            'empty_data' => '',
            'constraints' => [
                new NotBlank(),
                new GreaterThan(['value' => 0]),
            ],
        ]);
        $builder->add('allowedCharactersEnabled', BoolRequiredType::class, [
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
            'attr' => [
                'class' => 'js__textareaAutosize',
            ],
        ]);
        $builder->add('messageSystemEnabled', BoolRequiredType::class, [
            'label' => 'trans.Message system between users enabled',
            'required' => true,
            'constraints' => [
            ],
        ]);
        $builder->add('mapEnabled', BoolRequiredType::class, [
            'label' => 'trans.Map enabled?',
            'required' => true,
        ]);
        $builder->add('mapDefaultLatitude', NumberType::class, [
            'scale' => 12,
            'label' => 'trans.Map default latitude',
            'required' => true,
        ]);
        $builder->add('mapDefaultLongitude', NumberType::class, [
            'scale' => 12,
            'label' => 'trans.Map default longitude',
            'required' => true,
        ]);
        $builder->add('mapDefaultZoom', IntegerType::class, [
            'label' => 'trans.Map default zoom',
            'required' => true,
        ]);
        $builder->add('mapDefaultZoomSingleListing', IntegerType::class, [
            'label' => 'trans.Default zoom for map on single listing',
            'required' => true,
        ]);
        $builder->add('defaultAdvertisementZoneId', TextType::class, [
            'label' => 'trans.Default advertisement zone id',
            'required' => false,
            'help' => 'trans.used as fallback when category do not have advertisement zone id assigned',
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
