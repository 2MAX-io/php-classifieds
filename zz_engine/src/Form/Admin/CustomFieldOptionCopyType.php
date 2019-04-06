<?php

declare(strict_types=1);

namespace App\Form\Admin;

use App\Entity\CustomField;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class CustomFieldOptionCopyType extends AbstractType
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('sourceCustomField', EntityType::class, [
            'label' => 'trans.From custom field',
            'placeholder' => 'trans.Select',
            'class' => CustomField::class,
            'constraints' => [
                new NotBlank(),
            ],
            'choice_label' => static function (CustomField $customField) {
                $hint = '';
                if ($customField->getNameForAdmin()) {
                    $hint = ' ('.$customField->getNameForAdmin().')';
                }

                return $customField->getName().$hint;
            },
            'query_builder' => function () {
                $qb = $this->em->createQueryBuilder();
                $qb->select('customField');
                $qb->from(CustomField::class, 'customField');
                $qb->join('customField.customFieldOptions', 'customFieldOption');
                $qb->addOrderBy('customField.sort', Criteria::ASC);

                return $qb;
            },
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CustomFieldOptionCopyDto::class,
        ]);
    }
}
