<?php

declare(strict_types=1);

namespace App\Form\Admin\Settings;

use App\Form\Type\BoolType;
use App\Service\Setting\SettingsDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LoginSettingsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('facebookSignInEnabled', BoolType::class, [
            'label' => 'trans.Facebook Sign in enabled?',
            'required' => true,
        ]);
        $builder->add('facebookSignInAppId', TextType::class, [
            'label' => 'trans.Facebook Sign in App ID',
            'required' => true,
        ]);
        $builder->add('facebookSignInAppSecret', TextType::class, [
            'label' => 'trans.Facebook Sign in App Secret',
            'required' => true,
        ]);
        $builder->add('googleSignInEnabled', BoolType::class, [
            'label' => 'trans.Google Sign in enabled?',
            'required' => true,
        ]);
        $builder->add('googleSignInClientId', TextType::class, [
            'label' => 'trans.Google Sign in Client ID',
            'required' => true,
        ]);
        $builder->add('googleSignInClientSecret', TextType::class, [
            'label' => 'trans.Google Sign in Client Secret',
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
