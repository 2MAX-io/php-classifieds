<?php

namespace App\Form;

use App\Entity\Listing;
use App\Form\Type\CategoryType;
use App\Form\Type\FileSimpleType;
use App\Service\Listing\ValidityExtend\ValidUntilSetService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class ListingType extends AbstractType
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
        $builder
            ->add('title', TextType::class, [
                'label' => 'trans.Title',
                'empty_data' => '',
                'constraints' => [
                    new Constraints\NotBlank(),
                    new Constraints\Length(['min' => 5]),
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'trans.Description',
                'attr' => [
                    'class' => 'form-listing-description-textarea'
                ],
                'constraints' => [
                    new Constraints\NotBlank(),
                    new Constraints\Length(['min' => 20, 'max' => 5000]),
                ],
                'empty_data' => '',
            ])
            ->add('category', CategoryType::class, [
                'constraints' => [
                    new Constraints\NotBlank(),
                ],
            ])
            ->add('validityTimeDays', ChoiceType::class, [
                'mapped' => false,
                'choices' => $this->validUntilSetService->getValidityTimeDaysChoices(),
                'constraints' => [
                    new Constraints\Choice([
                        'choices' => $this->validUntilSetService->getValidityTimeDaysChoices()
                    ]),
                ],
                'label' => 'trans.Validity time',
                'empty_data' => 9,
            ])
            ->add('phone', TextType::class, [
                'label' => 'trans.Phone',
                'required' => false,
            ])
            ->add('email', EmailType::class, [
                'label' => 'trans.Email',
                'required' => false,
            ])
            ->add('emailShow', CheckboxType::class, [
                'label' => 'trans.Show email?',
                'required' => false,
            ])
            ->add('price', IntegerType::class, [
                'label' => 'trans.Price',
                'required' => false,
            ])
            ->add('city', TextType::class, [
                'label' => 'trans.City',
                'required' => false,
            ])
            ->add('customFields', HiddenType::class, [
                'mapped' => false,
                'attr' => [
                    'class' => 'formCustomFieldsHidden'
                ]
            ])
            ->add('file', FileSimpleType::class, [
                'mapped' => false,
                'required' => false,
                'multiple' => true,
                'constraints' => [
                    new Constraints\All(
                        new Constraints\Image(),
                    ),
                ],
                'attr' => ['hidden' => 'hidden'],
                'label' => 'trans.Pictures',
                'block_name' => 'simple',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => Listing::class,
                'constraints' => [
                    new Constraints\Callback(['callback' => function (Listing $listing, ExecutionContextInterface $context) {
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
