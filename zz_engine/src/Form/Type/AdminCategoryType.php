<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminCategoryType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'class' => Category::class,
            'placeholder' => 'trans.Select category',
            'label' => 'trans.Under category',
            'choice_label' => function (Category $category) {
                $path = $category->getPath();

                $path = array_map(
                    function (Category $category) {
                        return $category->getName();
                    },
                    $path
                );

                if (empty($path)) {
                    return $category->getName();
                }

                return join(' ⇾ ', $path);
            },
            'query_builder' => function (CategoryRepository $categoryRepository) {
                $qb = $categoryRepository->createQueryBuilder('category');
                $qb->addOrderBy('category.sort', 'ASC');

                return $qb;
            }
        ]);
    }

    public function getParent(): string
    {
        return EntityType::class;
    }
}
