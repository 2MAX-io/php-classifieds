<?php

declare(strict_types=1);

namespace App\Form\Admin;

use App\Entity\Category;
use App\Entity\Listing;
use App\Repository\CategoryRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminListingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title', TextType::class, [
            'label' => 'trans.Title'
        ]);
        $builder->add('description', TextareaType::class, [
            'label' => 'trans.Description',
            'attr' => [
                'class' => 'form-listing-description-textarea'
            ]
        ]);
        $builder->add(
            'category',
            EntityType::class,
            [
                'class' => Category::class,
                'placeholder' => 'trans.Select category',
                'label' => 'trans.Select category',
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
        );
        $builder->add('phone', TextType::class, [
            'label' => 'trans.Phone'
        ]);
        $builder->add('email', EmailType::class, [
            'label' => 'trans.Email'
        ]);
        $builder->add('price', IntegerType::class, [
            'label' => 'trans.Price'
        ]);
        $builder->add('city', TextType::class, [
            'label' => 'trans.City'
        ]);
        $builder->add('customFields', HiddenType::class, [
            'mapped' => false,
            'attr' => [
                'class' => 'formCustomFieldsHidden'
            ]
        ]);
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
