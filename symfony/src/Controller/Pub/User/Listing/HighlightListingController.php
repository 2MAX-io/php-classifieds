<?php

declare(strict_types=1);

namespace App\Controller\Pub\User\Listing;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HighlightListingController extends AbstractController
{
    /**
     * @Route("/user/highlight/{id}", name="app_user_highlight_listing")
     */
    public function validityExtend(): Response
    {
        return $this->render('user/listing/highlight_extend.html.twig', [
        ]);
    }
}
