<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\UserInvoiceDetails;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserInvoiceDetailsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('companyName');
        $builder->add('taxNumber');
        $builder->add('street');
        $builder->add('buildingNumber');
        $builder->add('unitNumber');
        $builder->add('city');
        $builder->add('zipCode');
        $builder->add('country');
        $builder->add('emailForInvoice');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserInvoiceDetails::class,
        ]);
    }
}
