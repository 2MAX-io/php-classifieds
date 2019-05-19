<?php

declare(strict_types=1);

namespace App\Controller\Admin\Other;

use App\Controller\Admin\Base\AbstractAdminController;
use App\Form\Admin\Secondary\AdminLogoUploadType;
use App\Service\Admin\Other\Logo\LogoUploadService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LogoUploadController extends AbstractAdminController
{
    /**
     * @Route("/admin/red5/logo-upload", name="app_admin_logo_upload")
     */
    public function index(Request $request, LogoUploadService $logoUploadService): Response
    {
        $form = $this->createForm(AdminLogoUploadType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
             $logoUploadService->saveLogo($form->get('logo')->getData());
        }

        return $this->render('admin/secondary/logo_upload/logo_upload.html.twig', [
            'form' => $form->createView(),
            'logoPath' => $logoUploadService->getLogoPath(),
        ]);
    }
}
