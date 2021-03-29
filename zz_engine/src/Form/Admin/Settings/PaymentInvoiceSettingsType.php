<?php

declare(strict_types=1);

namespace App\Form\Admin\Settings;

use App\Form\Admin\Settings\Base\SettingTypeInterface;
use App\Form\Type\BoolRequiredType;
use App\Service\Invoice\Enum\InvoiceGenerationStrategyEnum;
use App\Service\Payment\Enum\GatewayModeEnum;
use App\Service\Payment\Enum\PaymentGatewayEnum;
use App\Service\Setting\SettingsDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class PaymentInvoiceSettingsType extends AbstractType implements SettingTypeInterface
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('paymentAllowed', BoolRequiredType::class, [
            'label' => 'trans.Allow payments',
            'help' => 'trans.turning off will disable all monetization, featuring ads, top up of account',
            'required' => true,
            'constraints' => [
                new NotBlank(),
            ],
        ]);
        $builder->add('currency', TextType::class, [
            'label' => 'trans.Currency',
            'help' => 'trans.3 letters, ISO 4217 code',
            'required' => true,
            'constraints' => [
                new NotBlank(),
                new Length(['max' => 4]),
            ],
        ]);
        $builder->add('paymentGateway', ChoiceType::class, [
            'label' => 'trans.Payment Gateway',
            'required' => true,
            'placeholder' => 'trans.Select',
            'choices' => [
                'trans.payment_gateway.paypal' => PaymentGatewayEnum::PAYPAL,
                'trans.payment_gateway.przelewy24' => PaymentGatewayEnum::PRZELEWY24,
            ],
            'constraints' => [
                new NotBlank(),
            ],
        ]);
        $builder->add('paymentPayPalMode', ChoiceType::class, [
            'label' => 'trans.Payments - PayPal mode',
            'required' => true,
            'placeholder' => 'trans.Select',
            'choices' => [
                'trans.paypal.sandbox' => 'sandbox',
                'trans.paypal.live' => 'live',
            ],
            'constraints' => [
                new NotBlank(),
            ],
        ]);
        $builder->add('paymentPayPalClientId', TextType::class, [
            'label' => 'trans.Payments - PayPal Client ID',
            'required' => true,
        ]);
        $builder->add('paymentPayPalClientSecret', TextType::class, [
            'label' => 'trans.Payments - PayPal Client secret',
            'required' => true,
        ]);
        $builder->add('paymentPrzelewy24Mode', ChoiceType::class, [
            'label' => 'trans.Przelewy24 mode',
            'required' => true,
            'placeholder' => 'trans.Select',
            'choices' => [
                'trans.payment.przelewy24.sandbox' => GatewayModeEnum::SANDBOX,
                'trans.payment.przelewy24.live' => GatewayModeEnum::LIVE,
            ],
            'constraints' => [
                new NotBlank(),
            ],
        ]);
        $builder->add('paymentPrzelewy24MerchantId', TextType::class, [
            'label' => 'trans.Przelewy24 - Merchant id',
            'help' => 'trans.account login, seller id, p24_merchant_id, digits',
            'required' => false,
        ]);
        $builder->add('paymentPrzelewy24PosId', TextType::class, [
            'label' => 'trans.Przelewy24 - Pos id',
            'help' => 'trans.usually same as Merchant id, shop id, p24_pos_id',
            'required' => false,
        ]);
        $builder->add('paymentPrzelewy24Crc', TextType::class, [
            'label' => 'trans.Przelewy24 - CRC',
            'help' => 'trans.CRC key from account details',
            'required' => false,
        ]);
        $builder->add('paymentPayPalClientSecret', TextType::class, [
            'label' => 'trans.Payments - PayPal Client secret',
            'required' => true,
        ]);
        $builder->add('paymentGatewayPaymentDescription', TextType::class, [
            'label' => 'trans.Payment gateway - payment description',
            'help' => 'trans.used in payment gateway as description of what has been bought, ie on receipt',
            'required' => true,
            'constraints' => [
                new NotBlank(),
                new Length(['max' => 100]),
            ],
        ]);

        $builder->add('invoiceCompanyName', TextType::class, [
            'label' => 'trans.Company name',
            'required' => true,
        ]);
        $builder->add('invoiceTaxNumber', TextType::class, [
            'label' => 'trans.Tax Number',
            'required' => true,
        ]);
        $builder->add('invoiceCity', TextType::class, [
            'label' => 'trans.City',
            'required' => true,
        ]);
        $builder->add('invoiceStreet', TextType::class, [
            'label' => 'trans.Street',
            'required' => true,
        ]);
        $builder->add('invoiceBuildingNumber', TextType::class, [
            'label' => 'trans.Building number',
            'required' => true,
        ]);
        $builder->add('invoiceUnitNumber', TextType::class, [
            'label' => 'trans.Unit number',
            'required' => true,
        ]);
        $builder->add('invoiceZipCode', TextType::class, [
            'label' => 'trans.Zip code',
            'required' => true,
        ]);
        $builder->add('invoiceCountry', TextType::class, [
            'label' => 'trans.Country',
            'required' => true,
        ]);
        $builder->add('invoiceEmail', TextType::class, [
            'label' => 'trans.Seller email on invoice',
            'required' => true,
        ]);
        $builder->add('invoiceSoldItemDescription', TextType::class, [
            'label' => 'trans.Sold item description for invoice',
            'required' => true,
        ]);
        $builder->add('invoiceGenerationStrategy', ChoiceType::class, [
            'label' => 'trans.Invoice generation strategy',
            'required' => true,
            'choices' => [
                'trans.invoice_generation_strategy.disabled' => InvoiceGenerationStrategyEnum::DISABLED,
                'trans.invoice_generation_strategy.auto' => InvoiceGenerationStrategyEnum::AUTO,
                'trans.invoice_generation_strategy.external_system' => InvoiceGenerationStrategyEnum::EXTERNAL_SYSTEM,
            ],
        ]);
        $builder->add('invoiceNumberPrefix', TextType::class, [
            'label' => 'trans.Invoice number prefix',
            'help' => 'trans.can be empty',
            'required' => false,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => SettingsDto::class,
                'required' => false,
            ]
        );
    }
}
