<?php

declare(strict_types=1);

namespace App\Form\Admin;

use App\Entity\Page;
use App\Helper\Str;
use App\Validator\Constraints\Slug;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class PageType extends AbstractType
{
    const SLUG = 'slug';

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title', TextType::class, [
            'label' => 'trans.Title',
            'constraints' => [
                new NotBlank(),
            ],
        ]);
        $builder->add(
            self::SLUG, TextType::class, [
            'label' => 'trans.Slug',
            'constraints' => [
                new NotBlank(),
                new Slug(),
            ],
        ])
        ->addEventListener(FormEvents::PRE_SUBMIT, function(FormEvent $formEvent) {
            $data = $formEvent->getData();
            $data[self::SLUG] = Str::softSlug($data[self::SLUG]);

            $formEvent->setData($data);
        });
        $builder->add('content', TextareaType::class, [
            'label' => 'trans.Content',
        ]);
        $builder->add('enabled', CheckboxType::class, [
            'label' => 'trans.Enabled',
            'required' => false,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Page::class,
        ]);
    }
}