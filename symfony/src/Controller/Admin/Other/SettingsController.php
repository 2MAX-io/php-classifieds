<?php

declare(strict_types=1);

namespace App\Controller\Admin\Other;

use App\Controller\Admin\Base\AbstractAdminController;
use App\Form\Admin\SettingsType;
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

        $settingsDto = $settingsSaveService->getHydratedSettingsDto();
        $form = $this->createForm(SettingsType::class, $settingsDto);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $settingsSaveService->save($settingsDto);

            return $this->redirectToRoute('app_admin_settings');
        }

        return $this->render('admin/settings/settings.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
