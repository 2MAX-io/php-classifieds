<?php

declare(strict_types=1);

namespace App\Form\Admin;

use App\Entity\CustomField;
use App\Entity\CustomFieldJoinCategory;
use App\Repository\CustomFieldRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class CategoryAddCustomFieldType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('customField', EntityType::class, [
            'class' => CustomField::class,
            'placeholder' => 'trans.Select',
            'choice_label' => 'name',
            'label' => 'trans.Custom field',
            'query_builder' => function (CustomFieldRepository $customFieldRepository) {
                $qb = $customFieldRepository->createQueryBuilder('customField');

                $qb->addOrderBy('customField.sort', 'ASC');

                return $qb;
            },
            'constraints' => [
                new NotBlank(),
            ],
        ]);

        $builder->add('sort', IntegerType::class, [
            'label' => 'trans.Order, smaller first',
            'constraints' => [
                new NotBlank(),
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CustomFieldJoinCategory::class,
        ]);
    }
}
