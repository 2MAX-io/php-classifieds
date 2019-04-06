<?php

declare(strict_types=1);

namespace App\Controller\Admin\Secondary\Settings;

use App\Controller\Admin\Base\AbstractAdminController;
use App\Form\Admin\Settings\CustomizationSettingsType;
use App\Form\Admin\Settings\LicenseSettingsType;
use App\Form\Admin\Settings\LoginSettingsType;
use App\Form\Admin\Settings\PaymentInvoiceSettingsType;
use App\Form\Admin\Settings\SettingsType;
use App\Form\Admin\Settings\SystemSettingsType;
use App\Service\Setting\SettingsSaveService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminSettingsController extends AbstractAdminController
{
    /**
     * @Route("/admin/red5/settings", name="app_admin_settings")
     */
    public function settingsForAdmin(Request $request, SettingsSaveService $settingsSaveService): Response
    {
        $this->denyUnlessAdmin();

        $settingsDto = $settingsSaveService->getSettingsDtoWithoutCache();
        $form = $this->createForm(SettingsType::class, $settingsDto);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $settingsSaveService->save($settingsDto);

            return $this->redirectToRoute($request->get('_route'));
        }

        return $this->render('admin/settings/admin_settings.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/red5/settings/system-settings", name="app_admin_settings_system")
     */
    public function systemSettings(Request $request, SettingsSaveService $settingsSaveService): Response
    {
        $this->denyUnlessAdmin();

        $settingsDto = $settingsSaveService->getSettingsDtoWithoutCache();
        $form = $this->createForm(SystemSettingsType::class, $settingsDto);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $settingsSaveService->save($settingsDto);

            return $this->redirectToRoute($request->get('_route'));
        }

        return $this->render('admin/settings/system/system_settings.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/red5/settings/payment-invoice-settings", name="app_admin_settings_payment_invoice")
     */
    public function paymentInvoiceSettings(Request $request, SettingsSaveService $settingsSaveService): Response
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
     * @Route("/admin/red5/settings/login-settings", name="app_admin_settings_login")
     */
    public function loginSettings(Request $request, SettingsSaveService $settingsSaveService): Response
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

    /**
     * @Route("/admin/red5/settings/customization-settings", name="app_admin_settings_customization")
     */
    public function customizationSettings(Request $request, SettingsSaveService $settingsSaveService): Response
    {
        $this->denyUnlessAdmin();

        $settingsDto = $settingsSaveService->getSettingsDtoWithoutCache();
        $form = $this->createForm(CustomizationSettingsType::class, $settingsDto);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $settingsSaveService->save($settingsDto);

            return $this->redirectToRoute($request->get('_route'));
        }

        return $this->render('admin/settings/customization/customization_settings.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/red5/settings/license", name="app_admin_settings_license")
     */
    public function licenseSettings(Request $request, SettingsSaveService $settingsSaveService): Response
    {
        $this->denyUnlessAdmin();

        $settingsDto = $settingsSaveService->getSettingsDtoWithoutCache();
        $form = $this->createForm(LicenseSettingsType::class, $settingsDto);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $settingsDto->setLicenseValid(false);
            if (!empty($settingsDto->getLicense())) {
                $settingsDto->setLicenseValid(true);
            }
            $settingsSaveService->save($settingsDto);

            return $this->redirectToRoute($request->get('_route'));
        }

        return $this->render('admin/settings/license/license_settings.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
