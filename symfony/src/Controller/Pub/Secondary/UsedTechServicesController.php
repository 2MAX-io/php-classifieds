<?php

declare(strict_types=1);

namespace App\Controller\Pub\Secondary;

use App\Repository\PageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UsedTechServicesController extends AbstractController
{
    /**
     * @Route("/used-technologies-and-services", name="app_used_tech_serivces")
     */
    public function usedTechAndServices(PageRepository $pageRepository): Response {

        return $this->render('secondary/used_tech_services.html.twig', [
            'relatedPages' => $pageRepository->getRelatedPages(),
        ]);
    }
}
