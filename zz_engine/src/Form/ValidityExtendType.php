<?php

declare(strict_types=1);

namespace App\Form;

use App\Service\Listing\ValidityExtend\ValidUntilSetService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

class ValidityExtendType extends AbstractType
{
    /**
     * @var ValidUntilSetService
     */
    private $validUntilSetService;

    public function __construct(ValidUntilSetService $validUntilSetService)
    {
        $this->validUntilSetService = $validUntilSetService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('validityTimeDays', ChoiceType::class, [
            'mapped' => false,
            'choices' => $this->validUntilSetService->getValidityTimeDaysChoices(),
            'label' => 'trans.Counting from today, extend by',
            'data' => 9,
            'constraints' => [
                new Constraints\NotBlank(),
                new Constraints\Choice([
                    'choices' => $this->validUntilSetService->getValidityTimeDaysChoices()
                ]),
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => null,
            ]
        );
    }
}
