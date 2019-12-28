<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Listing;
use App\Form\Type\BoolType;
use App\Form\Type\CategoryType;
use App\Form\Type\AppMoneyType;
use App\Form\Type\PriceForType;
use App\Service\Listing\ValidityExtend\ValidUntilSetService;
use App\Validator\Constraints\HasLetterNumber;
use App\Validator\Constraints\Phone;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class ListingType extends AbstractType
{
    public const LISTING_FIELD = 'listing';
    public const CATEGORY_FIELD = 'category';

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
        $builder->add('title', TextType::class, [
                'label' => 'trans.Title',
                'empty_data' => '',
                'constraints' => [
                    new NotBlank(),
                    new Length(['min' => 5]),
                    new HasLetterNumber(),
                ],
                'attr' => [
                    'maxlength' => 70,
                ]
            ]);
        $builder->add('description', TextareaType::class, [
            'label' => 'trans.Description',
            'attr' => [
                'class' => 'form-listing-description-textarea textarea-autosize',
                'minlength' => 10,
                'maxlength' => 10000,
            ],
            'constraints' => [
                new NotBlank(),
                new Length(['min' => 10, 'max' => 10000]),
                new HasLetterNumber(),
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
            'label' => 'trans.For how long listing should be published?',
            'data' => 14,
        ]);
        $builder->add('phone', TextType::class, [
            'label' => 'trans.Phone',
            'required' => false,
            'constraints' => [
                new Phone(),
            ],
            'attr' => [
                'class' => 'input-phone',
                'maxlength' => 20,
            ],
        ]);
        $builder->add('email', EmailType::class, [
            'label' => 'trans.Email',
            'required' => false,
            'attr' => [
                'maxlength' => 70,
            ],
            'constraints' => [
                new Email([
                    'mode' => Email::VALIDATION_MODE_STRICT
                ]),
            ],
        ]);
        $builder->add('emailShow', CheckboxType::class, [
            'label' => 'trans.Show email?',
            'required' => false,
        ]);
        $builder->add('price', AppMoneyType::class, [
            'label' => 'trans.Amount or price',
            'required' => false,
            'constraints' => [
                new GreaterThanOrEqual([
                    'value' => 0,
                ]),
                new LessThanOrEqual([
                    'value' => (int) \str_repeat('9', 12),
                ]),
            ],
            'attr' => [
                'max' => (int) \str_repeat('9', 12),
                'class' => 'input-money',
            ],
        ]);
        $builder->add('priceNegotiable', BoolType::class, [
            'placeholder' => '—',
            'label' => 'trans.Amount is negotiable?',
            'required' => false,
        ]);
        $builder->add('priceFor', PriceForType::class, [
            'required' => false,
        ]);
        $builder->add('city', TextType::class, [
            'label' => 'trans.City',
            'required' => false,
            'attr' => [
                'maxlength' => 30,
            ],
            'constraints' => [
                new HasLetterNumber(),
            ],
        ]);
        $builder->add(
            ListingCustomFieldListType::CUSTOM_FIELD_LIST_FIELD,
            ListingCustomFieldListType::class,
            [
                'listingEntity' => $options['data'],
                'mapped' => false,
                'attr' => [
                    'class' => 'formCustomFieldList',
                ],
                'form_group_attr' => [
                    'class' => 'mb-0',
                ],
            ]
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => Listing::class,
                'constraints' => [
                    new Callback(['callback' => static function (Listing $listing, ExecutionContextInterface $context): void {
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

    public function getBlockPrefix(): string
    {
        return static::LISTING_FIELD;
    }
}
