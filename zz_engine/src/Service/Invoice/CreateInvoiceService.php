<?php

declare(strict_types=1);

namespace App\Service\Invoice;

use App\Entity\Invoice;
use App\Entity\Payment;
use App\Repository\UserInvoiceDetailsRepository;
use App\Service\Invoice\Enum\InvoiceGenerationStrategyEnum;
use App\Service\Invoice\Helper\InvoiceNumberGeneratorService;
use App\Service\Setting\SettingsService;
use Doctrine\ORM\EntityManagerInterface;

class CreateInvoiceService
{
    /**
     * @var SettingsService
     */
    private $settingsService;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var UserInvoiceDetailsRepository
     */
    private $userInvoiceDetailsRepository;

    /**
     * @var InvoiceNumberGeneratorService
     */
    private $invoiceNumberGeneratorService;

    public function __construct(
        UserInvoiceDetailsRepository $userInvoiceDetailsRepository,
        InvoiceNumberGeneratorService $invoiceNumberGeneratorService,
        SettingsService $settingsService,
        EntityManagerInterface $em
    ) {
        $this->settingsService = $settingsService;
        $this->em = $em;
        $this->userInvoiceDetailsRepository = $userInvoiceDetailsRepository;
        $this->invoiceNumberGeneratorService = $invoiceNumberGeneratorService;
    }

    public function createInvoice(Payment $payment): void
    {
        $settingsDto = $this->settingsService->getSettingsDtoWithoutCache();
        $invoiceGenerationStrategy = $settingsDto->getInvoiceGenerationStrategy();
        $user = $payment->getUser();
        if (null === $user) {
            throw new \UnexpectedValueException('user not found in payment when creating invoice');
        }
        $userInvoiceDetails = $this->userInvoiceDetailsRepository->findByUser($user);


        $invoice = new Invoice();
        $invoice->setUser($user);
        $invoice->setPayment($payment);
        $invoice->setDisplayToUser($this->getDisplayToUser());
        $invoice->setUuid(\uuid_create(\UUID_TYPE_RANDOM));
        $invoice->setExported(false);
        $invoice->setSentToUser(false);
        $invoice->setInvoiceFilePath(''); // todo
        $invoice->setCreatedDate(new \DateTime());
        $invoice->setUpdatedDate(new \DateTime());

        $invoice->setTotalMoney((string) ($payment->getAmount() / 100));
        $invoice->setCurrency($payment->getCurrency());

        // client

        if (null === $userInvoiceDetails) {
            $invoice->setCompanyName($user->getEmail()); // todo
        } else {
            $invoice->setCompanyName($userInvoiceDetails->getCompanyName());
            $invoice->setClientTaxNumber($userInvoiceDetails->getTaxNumber());
            $invoice->setCity($userInvoiceDetails->getCity());
            $invoice->setStreet($userInvoiceDetails->getStreet());
            $invoice->setBuildingNumber($userInvoiceDetails->getBuildingNumber());
            $invoice->setUnitNumber($userInvoiceDetails->getUnitNumber());
            $invoice->setZipCode($userInvoiceDetails->getZipCode());
            $invoice->setCountry($userInvoiceDetails->getCountry());
            $invoice->setEmailForInvoice($userInvoiceDetails->getEmailForInvoice());
        }

        // seller
        $invoice->setSellerCompanyName($settingsDto->getInvoiceCompanyName());
        $invoice->setSellerTaxNumber($settingsDto->getInvoiceTaxNumber());
        $invoice->setSellerCity($settingsDto->getInvoiceCity());
        $invoice->setSellerStreet($settingsDto->getInvoiceStreet());
        $invoice->setSellerBuildingNumber($settingsDto->getInvoiceBuildingNumber());
        $invoice->setSellerUnitNumber($settingsDto->getInvoiceUnitNumber());
        $invoice->setSellerZipCode($settingsDto->getInvoiceZipCode());
        $invoice->setSellerCountry($settingsDto->getInvoiceCountry());
        $invoice->setSellerEmail($settingsDto->getInvoiceEmail());

        $this->em->persist($invoice);
        $this->em->flush();

        if (InvoiceGenerationStrategyEnum::AUTO === $invoiceGenerationStrategy) {
            $invoiceNumber = $this->invoiceNumberGeneratorService->getNextInvoiceNumber($invoice);
            $invoice->setInvoiceNumber($invoiceNumber);
            $invoice->setInvoiceDate(new \DateTime());

            $this->em->persist($invoice);
            $this->em->flush();
        }
    }

    private function getDisplayToUser(): bool
    {
        $settingsDto = $this->settingsService->getSettingsDtoWithoutCache();
        $invoiceGenerationStrategy = $settingsDto->getInvoiceGenerationStrategy();
        if (InvoiceGenerationStrategyEnum::AUTO === $invoiceGenerationStrategy) {
            return true;
        }

        if (\in_array(
            $invoiceGenerationStrategy,
            [
                InvoiceGenerationStrategyEnum::MANUAL,
                InvoiceGenerationStrategyEnum::EXTERNAL_SYSTEM,
                InvoiceGenerationStrategyEnum::INFAKT_PL,
            ],
            true
        )) {
            return false;
        }

        throw new \UnexpectedValueException("could not find display to user value for strategy: {$invoiceGenerationStrategy}");
    }
}