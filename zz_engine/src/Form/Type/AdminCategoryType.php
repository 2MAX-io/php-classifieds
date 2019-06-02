<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminCategoryType extends AbstractType
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

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
            'query_builder' => function () {
                $qb = $this->em->getRepository(Category::class)->createQueryBuilder('category');
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
