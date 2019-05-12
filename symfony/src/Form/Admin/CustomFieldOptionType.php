<?php

declare(strict_types=1);

namespace App\Form\Admin;

use App\Entity\CustomField;
use App\Entity\CustomFieldOption;
use App\Validator\Constraints\UniqueValue;
use App\Validator\Constraints\UniqueValueDto;
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
                new UniqueValue([
                    'fields' => [
                        'value',
                        new UniqueValueDto('customField', $this->getCustomField($options)),
                    ],
                    'entityClass' => CustomFieldOption::class,
                    'excludeCurrent' => $this->getCustomFieldOption($options)
                ]),
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

    private function getCustomField(array $options): ?CustomField
    {
        if (!isset($options['data']) || !$options['data'] instanceof CustomFieldOption) {
            return null;
        }

        /** @var CustomFieldOption $customFieldOption */
        $customFieldOption = $options['data'];

        return $customFieldOption->getCustomField();
    }

    private function getCustomFieldOption(array $options): ?CustomFieldOption
    {
        if (!isset($options['data']) || !$options['data'] instanceof CustomFieldOption) {
            return null;
        }

        return $options['data'];
    }
}
