<?php

declare(strict_types=1);

namespace App\Controller\Pub\Listing;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LastAddedController extends AbstractController
{
    /**
     * @Route("/last-added", name="app_last_added")
     */
    public function index(): Response
    {
        return $this->render('last_added.html.twig', [
        ]);
    }
}
