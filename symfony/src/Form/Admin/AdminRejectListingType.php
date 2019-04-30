<?php

declare(strict_types=1);

namespace App\Form\Admin;

use App\Entity\Listing;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminRejectListingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('rejectionReason', TextareaType::class, [
            'label' => 'trans.Reject reason (optional)',
            'required' => false,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => Listing::class,
            ]
        );
    }
}
