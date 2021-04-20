<?php

declare(strict_types=1);

namespace App\Form\Payment;

use App\Entity\UserInvoiceDetails;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class UserInvoiceDetailsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('companyName', TextType::class, [
            'label' => 'trans.Company name',
            'required' => false,
        ]);
        $builder->add('taxNumber', TextType::class, [
            'label' => 'trans.Tax Number',
            'required' => false,
        ]);
        $builder->add('firstName', TextType::class, [
            'label' => 'trans.First Name',
            'required' => false,
            'help' => 'trans.for individual purchases',
        ]);
        $builder->add('lastName', TextType::class, [
            'label' => 'trans.Last Name',
            'required' => false,
            'help' => 'trans.for individual purchases',
        ]);
        $builder->add('street', TextType::class, [
            'label' => 'trans.Street',
        ]);
        $builder->add('buildingNumber', TextType::class, [
            'label' => 'trans.Building number',
        ]);
        $builder->add('unitNumber', TextType::class, [
            'label' => 'trans.Unit number',
            'required' => false,
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
        $builder->add('emailToSendInvoice', EmailType::class, [
            'label' => 'trans.Email to which send the invoice',
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserInvoiceDetails::class,
            'constraints' => [
                new Callback(['callback' => static function (
                    UserInvoiceDetails $userInvoiceDetails,
                    ExecutionContextInterface $context
                ): void {
                    $companyIsEmpty = empty($userInvoiceDetails->getCompanyName()) || empty($userInvoiceDetails->getTaxNumber());
                    $individualIsEmpty = empty($userInvoiceDetails->getFirstName()) || empty($userInvoiceDetails->getFirstName());
                    if ($companyIsEmpty && $individualIsEmpty) {
                        foreach (['companyName', 'taxNumber', 'firstName', 'lastName'] as $fieldName) {
                            $context->buildViolation('Enter company or individual details, bot can not be empty')
                                ->atPath($fieldName)
                                ->addViolation()
                            ;
                        }
                    }
                }]),
            ],
        ]);
    }
}
