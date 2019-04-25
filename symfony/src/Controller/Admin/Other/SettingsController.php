<?php

declare(strict_types=1);

namespace App\Controller\Admin\Other;

use App\Form\Admin\SettingsType;
use App\Service\Setting\SettingsDto;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SettingsController extends AbstractController
{
    /**
     * @Route("/admin/red5/settings", name="app_admin_settings")
     */
    public function settings(): Response
    {
        $settingsDto = new SettingsDto();
        $form = $this->createForm(SettingsType::class, $settingsDto);

        return $this->render('admin/settings/settings.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
