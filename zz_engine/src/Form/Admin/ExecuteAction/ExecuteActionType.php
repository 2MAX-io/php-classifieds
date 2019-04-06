<?php

declare(strict_types=1);

namespace App\Form\Admin\ExecuteAction;

use App\Entity\CustomFieldOption;
use App\Form\Type\CategoryType;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class ExecuteActionType extends AbstractType
{
    public const CUSTOM_FIELD_OPTION_FIELD = 'customFieldOption';
    public const CATEGORY_FIELD = 'category';
    public const ACTION = 'action';

    public const ACTION_SET_CUSTOM_FIELD_OPTION_WHEN_NOT_SET = 'ACTION_SET_CUSTOM_FIELD_OPTION_WHEN_NOT_SET';
    public const ACTION_SET_CATEGORY = 'ACTION_SET_CATEGORY';
    public const ACTION_REJECT = 'ACTION_REJECT';

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
        $builder->add(static::ACTION, ChoiceType::class, [
            'label' => 'trans.Execute action',
            'placeholder' => 'trans.Select',
            'choices' => [
                'trans.Set custom field option, if custom field not set' => static::ACTION_SET_CUSTOM_FIELD_OPTION_WHEN_NOT_SET,
                'trans.Set category' => static::ACTION_SET_CATEGORY,
                'trans.Reject listing' => static::ACTION_REJECT,
            ],
            'constraints' => [
                new NotBlank(),
            ],
            'attr' => [
                'class' => 'js__selectAction',
            ],
        ]);
        $builder->add(static::CUSTOM_FIELD_OPTION_FIELD, EntityType::class, [
            'label' => 'trans.Custom field option',
            'placeholder' => 'trans.Select',
            'class' => CustomFieldOption::class,
            'choice_label' => static function (CustomFieldOption $customFieldOption) {
                $customField = $customFieldOption->getCustomFieldNotNull();
                $hint = $customField->getNameForAdmin() ? " ({$customField->getNameForAdmin()})" : '';

                return $customField->getName().$hint.' ⇾ '.$customFieldOption->getName();
            },
            'query_builder' => function () {
                $qb = $this->em->createQueryBuilder();
                $qb->select('customFieldOption');
                $qb->from(CustomFieldOption::class, 'customFieldOption');
                $qb->join('customFieldOption.customField', 'customField');

                $qb->addOrderBy('customField.sort', Criteria::ASC);
                $qb->addOrderBy('customFieldOption.sort', Criteria::ASC);

                return $qb;
            },
            'form_group_attr' => [
                'class' => 'js__executeActionValueSelect d-none-soft',
                'data-value-for-action' => static::ACTION_SET_CUSTOM_FIELD_OPTION_WHEN_NOT_SET,
            ],
        ]);
        $builder->add(static::CATEGORY_FIELD, CategoryType::class, [
            'label' => 'trans.Category',
            'placeholder' => 'trans.Select',
            'form_group_attr' => [
                'class' => 'js__executeActionValueSelect d-none-soft',
                'data-value-for-action' => static::ACTION_SET_CATEGORY,
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'required' => false,
            'data_class' => ExecuteActionDto::class,
            'constraints' => [
                new Callback(['callback' => static function (ExecuteActionDto $executeActionDto, ExecutionContextInterface $context): void {
                    $action = $executeActionDto->getAction();

                    if ($action === static::ACTION_SET_CUSTOM_FIELD_OPTION_WHEN_NOT_SET && null === $executeActionDto->getCustomFieldOption()) {
                        $context->buildViolation('This value should not be blank.')
                            ->atPath(static::CUSTOM_FIELD_OPTION_FIELD)
                            ->addViolation()
                        ;
                    }

                    if ($action === static::ACTION_SET_CATEGORY && null === $executeActionDto->getCategory()) {
                        $context->buildViolation('This value should not be blank.')
                            ->atPath(static::CATEGORY_FIELD)
                            ->addViolation()
                        ;
                    }
                }]),
            ],
        ]);
    }
}
