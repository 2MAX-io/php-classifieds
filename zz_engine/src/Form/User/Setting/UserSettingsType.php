<?php

declare(strict_types=1);

namespace App\Form\User\Setting;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;

class UserSettingsType extends AbstractType
{
    public const DISPLAY_USERNAME = 'displayUsername';

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(static::DISPLAY_USERNAME, TextType::class, [
            'label' => 'trans.User name to display publicly',
            'required' => false,
            'constraints' => [
                new Length(['max' => 36]),
            ],
        ]);
    }
}
