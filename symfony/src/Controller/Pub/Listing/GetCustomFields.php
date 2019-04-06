<?php

declare(strict_types=1);

namespace App\Controller\Pub\Listing;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GetCustomFields extends AbstractController
{
    /**
     * @Route("/listing/get-custom-fields", name="app_listing_get_custom_fields")
     */
    public function getCustomFields(): Response
    {
        return $this->render('listing/get_custom_fields.html.twig');
    }
}

