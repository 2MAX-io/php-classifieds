<?php

declare(strict_types=1);

namespace App\Form\Admin;

use App\Entity\Category;
use App\Form\Type\AdminCategoryType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminCategorySaveType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', TextType::class, [
            'label' => 'trans.Name',
        ]);
        $builder->add('slug', TextType::class, [
            'label' => 'trans.Slug',
        ]);
        $builder->add('parent', AdminCategoryType::class);
        $builder->add('sort', IntegerType::class, [
            'label' => 'trans.Order, smaller first',
        ]);
        $builder->add('picture', FileType::class, [
            'label' => 'trans.Picture',
            'required' => false,
            'mapped' => false,
        ]);
//        $builder->add('customFields');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Category::class,
        ]);
    }
}
