<?php

declare(strict_types=1);

namespace App\Controller\Pub;

use App\Entity\Page;
use App\Repository\PageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PageShowController extends AbstractController
{
    /**
     * @Route("/p/{slug}", name="app_page")
     */
    public function pageShow(Page $page, PageRepository $pageRepository): Response
    {
        if (false === $page->getEnabled()) {
            throw $this->createNotFoundException();
        }

        return $this->render('secondary/page_show.html.twig', [
            'page' => $page,
            'relatedPages' => $pageRepository->getRelatedPages(),
        ]);
    }
}
