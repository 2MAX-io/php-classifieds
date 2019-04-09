<?php

declare(strict_types=1);

namespace App\Controller\Pub\User\Listing;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ValidityExtendController extends AbstractController
{
    /**
     * @Route("/user/validity-extend/{id}", name="app_user_validity_extend")
     */
    public function validityExtend(): Response
    {
        return $this->render('user/listing/validity_extend.html.twig', [
        ]);
    }
}
