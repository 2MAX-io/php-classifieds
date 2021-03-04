<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\UserInvoiceDetails;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserInvoiceDetailsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('companyName', TextType::class, [
            'label' => 'trans.Company name',
        ]);
        $builder->add('taxNumber', TextType::class, [
            'label' => 'trans.Tax Number',
        ]);
        $builder->add('street', TextType::class, [
            'label' => 'trans.Street',
        ]);
        $builder->add('buildingNumber', TextType::class, [
            'label' => 'trans.Building number',
        ]);
        $builder->add('unitNumber', TextType::class, [
            'label' => 'trans.Unit number',
        ]);
        $builder->add('city', TextType::class, [
            'label' => 'trans.City',
        ]);
        $builder->add('zipCode', TextType::class, [
            'label' => 'trans.Zip code',
        ]);
        $builder->add('country', TextType::class, [
            'label' => 'trans.Country',
        ]);
        $builder->add('emailForInvoice', TextType::class, [
            'label' => 'trans.Email for invoice',
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserInvoiceDetails::class,
        ]);
    }
}
