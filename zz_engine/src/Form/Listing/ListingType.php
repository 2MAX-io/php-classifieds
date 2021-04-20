<?php

declare(strict_types=1);

namespace App\Form\Listing;

use App\Entity\Listing;
use App\Form\Type\AppMoneyType;
use App\Form\Type\BoolType;
use App\Form\Type\CategoryType;
use App\Form\Type\PriceForType;
use App\Service\Listing\Save\SaveListingService;
use App\Service\Setting\SettingsDto;
use App\Validator\Constraints\HasLetterNumber;
use App\Validator\Constraints\Phone;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class ListingType extends AbstractType
{
    public const LISTING_FIELD = 'listing';
    public const PACKAGE_FIELD = 'package';
    public const CATEGORY_FIELD = 'category';
    public const DESCRIPTION_MAX_LENGTH = 10000;

    /**
     * @var SaveListingService
     */
    private $saveListingService;

    /**
     * @var SettingsDto
     */
    private $settingsDto;

    public function __construct(
        SaveListingService $saveListingService,
        SettingsDto $settingsDto
    ) {
        $this->saveListingService = $saveListingService;
        $this->settingsDto = $settingsDto;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var Listing $listing */
        $listing = $options['data'];

        $builder->add('title', TextType::class, [
            'label' => 'trans.Title',
            'empty_data' => '',
            'constraints' => [
                new NotBlank(),
                new Length(['min' => 5, 'allowEmptyString' => false]),
                new HasLetterNumber(),
            ],
            'attr' => [
                'maxlength' => 70,
            ],
        ]);
        $builder->add('description', TextareaType::class, [
            'label' => 'trans.Description',
            'attr' => [
                'class' => 'edit-listing-description-textarea js__textareaAutosize',
                'minlength' => 10,
                'maxlength' => static::DESCRIPTION_MAX_LENGTH,
            ],
            'constraints' => [
                new NotBlank(),
                new Length([
                    'min' => 10,
                    'max' => static::DESCRIPTION_MAX_LENGTH + 1000,
                    'maxMessage' => 'This value is too long. It should have 10 000 character or less.|This value is too long. It should have 10 000 characters or less.',
                    'allowEmptyString' => false,
                ]),
                new HasLetterNumber(),
            ],
            'empty_data' => '',
        ]);
        $builder->add('category', CategoryType::class, [
            'constraints' => [
                new NotBlank(),
            ],
        ]);
        $builder->add('phone', TextType::class, [
            'label' => 'trans.Phone',
            'required' => false,
            'constraints' => [
                new Phone(),
            ],
            'attr' => [
                'maxlength' => 20,
                'class' => 'js__inputPhone',
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
                    'mode' => Email::VALIDATION_MODE_STRICT,
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
                'class' => 'js__inputMoney',
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
        $builder->add('location', TextType::class, [
            'label' => 'trans.Location',
            'required' => false,
            'attr' => [
                'maxlength' => 30,
            ],
            'constraints' => [
                new HasLetterNumber(),
            ],
        ]);
        $builder->add(
            ListingCustomFieldsType::CUSTOM_FIELD_LIST_FIELD,
            ListingCustomFieldsType::class,
            [
                'listingEntity' => $options['data'],
                'mapped' => false,
                'attr' => [
                    'class' => 'js__customFieldList',
                ],
                'form_group_attr' => [
                    'class' => 'mb-0',
                ],
            ]
        );
        $builder->add('locationLatitude', HiddenType::class, [
            'label' => false,
            'required' => false,
            'attr' => [
                'class' => 'js__locationLatitude',
            ],
        ]);
        $builder->add('locationLongitude', HiddenType::class, [
            'label' => false,
            'required' => false,
            'attr' => [
                'class' => 'js__locationLongitude',
            ],
        ]);
        $builder->add(static::PACKAGE_FIELD, SelectPackageType::class, [
            'label' => 'trans.Pick listing publication option',
            'mapped' => false,
            'category' => $listing->getCategory(),
            'required' => $listing->isExpired(),
            'constraints' => (static function () use ($listing) {
                $constraints = [];
                if ($listing->isExpired()) {
                    $constraints[] = new NotBlank();
                }

                return $constraints;
            })(),
        ]);

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $formEvent): void {
            $listingFormDataArray = $formEvent->getData();
            $formEvent->setData($this->saveListingService->modifyListingPreFormSubmit($listingFormDataArray));
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => Listing::class,
                'constraints' => [
                    new Callback(['callback' => function (Listing $listing, ExecutionContextInterface $context): void {
                        if ($this->settingsDto->getMessageSystemEnabled() && $listing->getUserNotNull()->getMessagesEnabled()) {
                            return;
                        }

                        if (!$listing->hasContactData()) {
                            $context->buildViolation('Enter email or phone, both can not be empty')
                                ->atPath('phone')
                                ->addViolation()
                            ;

                            $context->buildViolation('Enter email or phone, both can not be empty')
                                ->atPath('email')
                                ->addViolation()
                            ;

                            $context->buildViolation('Enter email or phone, both can not be empty')
                                ->atPath('emailShow')
                                ->addViolation()
                            ;
                        }
                    }]),
                ],
            ]
        );
    }

    public function getBlockPrefix(): string
    {
        return static::LISTING_FIELD;
    }
}
