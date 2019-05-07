<?php

declare(strict_types=1);

namespace App\Form\Admin;

use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminCategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name');
        $builder->add('slug');
        $builder->add('sort');
//        $builder->add('lft');
//        $builder->add('rgt');
//        $builder->add('lvl');
        $builder->add('picture');
//        $builder->add('parent');
//        $builder->add('customFields');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Category::class,
        ]);
    }
}
