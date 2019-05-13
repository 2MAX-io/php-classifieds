<?php

declare(strict_types=1);

namespace App\Form\Admin;

use App\Entity\CustomFieldOption;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class CustomFieldOptionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', TextType::class, [
            'label' => 'trans.Name',
        ]);
        $builder->add('value', TextType::class, [
            'label' => 'trans.Value',
            'constraints' => [
                new NotBlank(),
            ]
        ]);
        $builder->add('sort', IntegerType::class, [
            'label' => 'trans.Order, smaller first',
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CustomFieldOption::class,
        ]);
    }
}
