<?php

declare(strict_types=1);

namespace App\Form\Admin;

use App\Entity\Page;
use App\Helper\SlugHelper;
use App\Validator\Constraints\HasLetterNumber;
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
    public const SLUG = 'slug';

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('title', TextType::class, [
            'label' => 'trans.Title',
            'constraints' => [
                new NotBlank(),
            ],
        ]);
        $slug = $builder->add(self::SLUG, TextType::class, [
            'label' => 'trans.Slug',
            'constraints' => [
                new NotBlank(),
                new Slug(),
                new HasLetterNumber(),
            ],
        ]);
        $slug->addEventListener(FormEvents::PRE_SUBMIT, static function (FormEvent $formEvent): void {
            $data = $formEvent->getData();
            $data[self::SLUG] = SlugHelper::lowercaseWithoutSpaces($data[self::SLUG]);

            $formEvent->setData($data);
        });
        $builder->add('content', TextareaType::class, [
            'label' => 'trans.Content',
            'attr' => [
                'class' => 'js__textareaAutosize',
            ],
        ]);
        $builder->add('enabled', CheckboxType::class, [
            'label' => 'trans.Enabled',
            'required' => false,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Page::class,
        ]);
    }
}
