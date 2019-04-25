<?php

namespace App\Form;

use App\Entity\Page;
use App\Validator\Constraints\UniqueValue;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('slug', TextType::class, [
            'label' => 'trans.Slug',
            'constraints' => [
                new UniqueValue(['fields' => 'slug', 'entityClass' => Page::class]),
            ],
        ]);
        $builder->add('title', TextType::class, [
            'label' => 'trans.Title',
        ]);
        $builder->add('content', TextareaType::class, [
            'label' => 'trans.Content',
        ]);
        $builder->add('enabled', CheckboxType::class, [
            'label' => 'trans.Enabled',
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Page::class,
        ]);
    }
}
