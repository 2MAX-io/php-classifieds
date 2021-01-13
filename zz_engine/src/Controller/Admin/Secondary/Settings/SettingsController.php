<?php

declare(strict_types=1);

namespace App\Controller\Admin\Secondary\Settings;

use App\Controller\Admin\Base\AbstractAdminController;
use App\Form\Admin\Settings\LoginSettingsType;
use App\Form\Admin\Settings\PaymentInvoiceSettingsType;
use App\Form\Admin\Settings\SettingsType;
use App\Service\Setting\SettingsService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SettingsController extends AbstractAdminController
{
    /**
     * @Route("/admin/red5/settings", name="app_admin_settings")
     */
    public function settings(Request $request, SettingsService $settingsSaveService): Response
    {
        $this->denyUnlessAdmin();

        $settingsDto = $settingsSaveService->getSettingsDtoWithoutCache();
        $form = $this->createForm(SettingsType::class, $settingsDto);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $settingsSaveService->save($settingsDto);

            return $this->redirectToRoute($request->get('_route'));
        }

        return $this->render('admin/settings/settings.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/red5/settings/payment-invoice-settings", name="app_admin_settings_payment_invoice")
     */
    public function paymentInvoiceSettings(Request $request, SettingsService $settingsSaveService): Response
    {
        $this->denyUnlessAdmin();

        $settingsDto = $settingsSaveService->getSettingsDtoWithoutCache();
        $form = $this->createForm(PaymentInvoiceSettingsType::class, $settingsDto);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $settingsSaveService->save($settingsDto);

            return $this->redirectToRoute($request->get('_route'));
        }

        return $this->render('admin/settings/payment_invoice/payment_invoice_settings.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/red5/settings/lgoin-settings", name="app_admin_settings_login")
     */
    public function loginSettings(Request $request, SettingsService $settingsSaveService): Response
    {
        $this->denyUnlessAdmin();

        $settingsDto = $settingsSaveService->getSettingsDtoWithoutCache();
        $form = $this->createForm(LoginSettingsType::class, $settingsDto);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $settingsSaveService->save($settingsDto);

            return $this->redirectToRoute($request->get('_route'));
        }

        return $this->render('admin/settings/login/login_settings.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
