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
                'label' => 'trans.Title'
            ])
            ->add('description', TextareaType::class, [
                'label' => 'trans.Description',
                'attr' => [
                    'class' => 'form-listing-description-textarea'
                ],
                'constraints' => [
                    new Constraints\Length(['min' => 20, 'max' => 5000]),
                ],
            ])
            ->add('category', CategoryType::class)
            ->add('validityTimeDays', ChoiceType::class, [
                'mapped' => false,
                'choices' => $this->validUntilSetService->getValidityTimeDaysChoices(),
                'constraints' => [
                    new Constraints\Choice([
                        'choices' => $this->validUntilSetService->getValidityTimeDaysChoices()
                    ]),
                ],
                'label' => 'trans.Validity time',
                'data' => 9,
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
//                    new Assert\File(['mimeTypes']) // todo: mime validation
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
            ]
        );
    }
}
