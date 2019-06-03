<?php

declare(strict_types=1);

namespace App\Form\Admin;

use App\Entity\Category;
use App\Form\Type\AdminCategoryType;
use App\Helper\Str;
use App\Validator\Constraints\Slug;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\NotBlank;

class AdminCategorySaveType extends AbstractType
{
    public const SLUG = 'slug';
    public const SAVE_AND_ADD = 'saveAndAdd';

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('name', TextType::class, [
            'label' => 'trans.Name',
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
        ->addEventListener(FormEvents::PRE_SUBMIT, static function(FormEvent $formEvent): void {
            $data = $formEvent->getData();
            $data[self::SLUG] = Str::softSlug($data[self::SLUG]);

            $formEvent->setData($data);
        });
        $builder->add('parent', AdminCategoryType::class, [
            'constraints' => [
                new NotBlank(),
            ],
        ]);
        $builder->add('sort', IntegerType::class, [
            'label' => 'trans.Order, smaller first',
            'constraints' => [
                new NotBlank(),
            ],
        ]);
        $builder->add('picture', FileType::class, [
            'label' => 'trans.Picture',
            'required' => false,
            'mapped' => false,
            'constraints' => [
                new Image(),
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Category::class,
        ]);
    }
}
