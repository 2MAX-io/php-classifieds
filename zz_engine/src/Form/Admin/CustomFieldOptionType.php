<?php

declare(strict_types=1);

namespace App\Form\Admin;

use App\Entity\CustomFieldOption;
use App\Helper\Str;
use App\Validator\Constraints\Value;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class CustomFieldOptionType extends AbstractType
{
    const VALUE_FIELD = 'value';
    const SAVE_AND_ADD = 'saveAndAdd';

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('name', TextType::class, [
            'label' => 'trans.Name',
            'constraints' => [
                new NotBlank(),
            ],
        ]);
        $builder->add(
            self::VALUE_FIELD, TextType::class, [
            'label' => 'trans.Value',
            'constraints' => [
                new NotBlank(),
                new Value(),
            ]
        ])
        ->addEventListener(FormEvents::PRE_SUBMIT, function(FormEvent $formEvent) {
            $data = $formEvent->getData();
            $data[self::VALUE_FIELD] = Str::softSlug($data[self::VALUE_FIELD]);

            $formEvent->setData($data);
        });
        $builder->add('sort', IntegerType::class, [
            'label' => 'trans.Order, smaller first',
            'constraints' => [
                new NotBlank(),
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CustomFieldOption::class,
        ]);
    }
}
