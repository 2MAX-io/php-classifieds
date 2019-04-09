<?php

declare(strict_types=1);

namespace App\Controller\Pub;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryViewAllController extends AbstractController
{
    /**
     * @Route("/categories", name="app_category_view_all")
     */
    public function validityExtend(): Response
    {
        return $this->render('category_view_all.html.twig', [
        ]);
    }
}
