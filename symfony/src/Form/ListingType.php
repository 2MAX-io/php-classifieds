<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Listing;
use App\Form\Type\BoolType;
use App\Form\Type\CategoryType;
use App\Form\Type\CustomMoneyType;
use App\Form\Type\FileSimpleType;
use App\Form\Type\PriceForType;
use App\Service\Listing\ValidityExtend\ValidUntilSetService;
use App\Service\Setting\SettingsService;
use App\Validator\Constraints\Phone;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class ListingType extends AbstractType
{
    /**
     * @var ValidUntilSetService
     */
    private $validUntilSetService;

    /**
     * @var SettingsService
     */
    private $settingsService;

    public function __construct(ValidUntilSetService $validUntilSetService, SettingsService $settingsService)
    {
        $this->validUntilSetService = $validUntilSetService;
        $this->settingsService = $settingsService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title', TextType::class, [
                'label' => 'trans.Title',
                'empty_data' => '',
                'constraints' => [
                    new NotBlank(),
                    new Length(['min' => 5]),
                ],
            ]);
        $builder->add('description', TextareaType::class, [
            'label' => 'trans.Description',
            'attr' => [
                'class' => 'form-listing-description-textarea'
            ],
            'constraints' => [
                new NotBlank(),
                new Length(['min' => 20, 'max' => 10000]),
            ],
            'empty_data' => '',
        ]);
        $builder->add('category', CategoryType::class, [
            'constraints' => [
                new NotBlank(),
            ],
        ]);
        $builder->add('validityTimeDays', ChoiceType::class, [
            'mapped' => false,
            'choices' => $this->validUntilSetService->getValidityTimeDaysChoices(),
            'constraints' => [
                new NotBlank(),
                new Choice([
                    'choices' => $this->validUntilSetService->getValidityTimeDaysChoices()
                ]),
            ],
            'label' => 'trans.Validity time',
            'empty_data' => 9,
        ]);
        $builder->add('phone', TextType::class, [
            'label' => 'trans.Phone',
            'required' => false,
            'constraints' => [
                new Phone(),
            ],
            'attr' => [
                'class' => 'input-phone',
            ],
        ]);
        $builder->add('email', EmailType::class, [
            'label' => 'trans.Email',
            'required' => false,
        ]);
        $builder->add('emailShow', CheckboxType::class, [
            'label' => 'trans.Show email?',
            'required' => false,
        ]);
        $builder->add('price', CustomMoneyType::class, [
            'label' => 'trans.Amount or price',
            'required' => false,
            'constraints' => [
                new GreaterThanOrEqual([
                    'value' => 0,
                ]),
            ],
        ]);
        $builder->add('priceNegotiable', BoolType::class, [
            'label' => 'trans.Amount is negotiable?',
            'required' => false,
        ]);
        $builder->add('priceFor', PriceForType::class, [
            'required' => false,
        ]);
        $builder->add('city', TextType::class, [
            'label' => 'trans.City',
            'required' => false,
        ]);
        $builder->add('customFields', HiddenType::class, [
            'mapped' => false,
            'attr' => [
                'class' => 'formCustomFieldsHidden'
            ]
        ]);
        $builder->add('file', FileSimpleType::class, [
            'mapped' => false,
            'required' => false,
            'multiple' => true,
            'constraints' => [
                new All(
                    new Image()
                ),
            ],
            'attr' => ['hidden' => 'hidden'],
            'label' => 'trans.Pictures',
            'block_name' => 'simple',
        ]);
        $builder->add('customFieldList', ListingCustomFieldListType::class, [
            'listingEntity' => $options['data'],
            'mapped' => false,
            'required' => false,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => Listing::class,
                'allow_extra_fields' => true, // todo: remove
                'constraints' => [
                    new Callback(['callback' => function (Listing $listing, ExecutionContextInterface $context) {
                        if (empty($listing->getPhone()) && (empty($listing->getEmail()) || !$listing->getEmailShow())) {
                            $context->buildViolation('Enter email or phone, both can not be empty')
                                ->atPath('phone')
                                ->addViolation();

                            $context->buildViolation('Enter email or phone, both can not be empty')
                                ->atPath('email')
                                ->addViolation();

                            $context->buildViolation('Enter email or phone, both can not be empty')
                                ->atPath('emailShow')
                                ->addViolation();
                        }
                    }])
                ],
            ]
        );
    }
}
