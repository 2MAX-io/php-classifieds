<?php

declare(strict_types=1);

namespace App\Controller\Pub\Secondary;

use App\Repository\PageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Page that mentions used free pictures, open source tech etc.
 */
class CreditsForContributorsController extends AbstractController
{
    /**
     * @Route("/credits-for-contributors", name="app_credits")
     */
    public function credits(PageRepository $pageRepository): Response {

        return $this->render('secondary/credits_for_contributors.html.twig', [
            'relatedPages' => $pageRepository->getRelatedPages(),
        ]);
    }
}
