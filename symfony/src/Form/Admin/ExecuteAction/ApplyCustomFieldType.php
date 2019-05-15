<?php

declare(strict_types=1);

namespace App\Form\Admin\ExecuteAction;

use App\Entity\CustomFieldOption;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ApplyCustomFieldType extends AbstractType
{
    public const CUSTOM_FIELD_OPTION = 'customFieldOption';

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(static::CUSTOM_FIELD_OPTION, EntityType::class, [
            'label' => 'trans.Custom field option',
            'placeholder' => 'trans.Select',
            'class' => CustomFieldOption::class,
            'choice_label' => function (CustomFieldOption $customFieldOption) {
                return $customFieldOption->getCustomField()->getName() . ' - ' . $customFieldOption->getName();
            },
            'query_builder' => function () {
                $qb = $this->em->getRepository(CustomFieldOption::class)->createQueryBuilder('customFieldOption');
                $qb->join('customFieldOption.customField', 'customField');

                $qb->addOrderBy('customField.sort', 'ASC');
                $qb->addOrderBy('customFieldOption.sort', 'ASC');

                return $qb;
            },
            'constraints' => [
                new NotBlank(),
            ]
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }
}
