<?php

declare(strict_types=1);

namespace App\Form;

use App\Service\Listing\ValidityExtend\ValidUntilSetService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TopUpBalanceType extends AbstractType
{
    /**
     * @var ValidUntilSetService
     */
    private $validUntilSetService;

    public function __construct(ValidUntilSetService $validUntilSetService)
    {
        $this->validUntilSetService = $validUntilSetService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('topUpAmount', MoneyType::class, [
            'mapped' => false,
            'label' => 'trans.Top up amount',
            'attr' => [
                'class' => 'input-money'
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => null,
            ]
        );
    }
}
