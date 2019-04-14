<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Listing;
use App\Form\Type\FileSimpleType;
use App\Repository\CategoryRepository;
use App\Service\Listing\ValidityExtend\ValidUntilSetService;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
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
                ]
            ])
            ->add(
                'category',
                EntityType::class,
                [
                    'class' => Category::class,
                    'placeholder' => 'trans.Select category',
                    'label' => 'trans.Category',
                    'attr' => [
                        'class' => 'formCategory',
                    ],
                    'choice_label' => function (Category $category, $key, $value) {
                        $path = $category->getPath();

                        $path = array_map(
                            function (Category $category) {
                                if ($category->getLvl() < 1) {
                                    return false;
                                }

                                return $category->getName();
                            },
                            $path
                        );

                        return join(' ⇾ ', $path);
                    },
                    'query_builder' => function (CategoryRepository $categoryRepository) {
                        $qb = $categoryRepository->createQueryBuilder('category');

                        $qb->andWhere('category.lvl > 1');
                        $qb->addOrderBy('category.lft', 'DESC');

                        return $qb;
                    }
                ]
            )
            ->add('price', IntegerType::class, [
                'label' => 'trans.Price'
            ])
            ->add('phone', TextType::class, [
                'label' => 'trans.Phone'
            ])
            ->add('email', EmailType::class, [
                'label' => 'trans.Email'
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
                'data' => 9,
            ])
            ->add('city', TextType::class, [
                'label' => 'trans.City'
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
