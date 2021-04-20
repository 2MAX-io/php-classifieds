<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\Entity\Category;
use Doctrine\Common\Collections\Criteria;
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
            'choice_label' => static function (Category $category) {
                return $category->getPathString();
            },
            'query_builder' => function () {
                $qb = $this->em->createQueryBuilder();
                $qb->select('category');
                $qb->from(Category::class, 'category');
                $qb->addOrderBy('category.sort', Criteria::ASC);

                return $qb;
            },
        ]);
    }

    public function getParent(): string
    {
        return EntityType::class;
    }
}
