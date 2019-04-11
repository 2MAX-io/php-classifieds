<?php

declare(strict_types=1);

namespace App\Controller\Pub\Listing;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LastAddedController extends AbstractController
{
    /**
     * remove if not used, not used from 2019-04-11
     */
    public function index(): Response
    {
        return $this->render('last_added.html.twig', [
        ]);
    }
}
