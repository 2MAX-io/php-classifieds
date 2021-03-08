<?php

declare(strict_types=1);

namespace App\Form\Secondary;

use App\Entity\System\ListingReport;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ListingReportType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('reportMessage', TextareaType::class, [
            'label' => 'trans.Reason for the report',
            'attr' => [
                'rows' => 5,
                'class' => 'js__textareaAutosize',
                'maxlength' => 3000,
            ],
            'constraints' => [
                new NotBlank(),
            ],
        ]);
        $builder->add('email', EmailType::class, [
            'label' => 'trans.Email',
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ListingReport::class,
        ]);
    }
}
