<?php

declare(strict_types=1);

namespace App\Form\Listing\ExtendExpiration;

use App\Form\Listing\SelectPackageType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ExtendExpirationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('package', SelectPackageType::class, [
            'label' => 'trans.Select listing extend option',
            'required' => true,
            'category' => $options['category'],
            'constraints' => [
                new NotBlank(),
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ExtendExpirationDto::class,
        ]);

        $resolver->setDefined(['category']);
    }
}
