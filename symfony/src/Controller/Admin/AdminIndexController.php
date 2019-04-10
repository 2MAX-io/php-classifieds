<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminIndexController extends AbstractController
{
    /**
     * @Route("/admin/red5/index", name="app_admin_index")
     * @Route("/admin/red5/")
     */
    public function adminIndex(): Response
    {
        return $this->render('admin/index.html.twig', [
        ]);
    }
}
