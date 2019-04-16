<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategoryType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
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
                $qb->addOrderBy('category.sort', 'ASC');

                return $qb;
            }
        ]);
    }

    public function getParent()
    {
        return EntityType::class;
    }
}
