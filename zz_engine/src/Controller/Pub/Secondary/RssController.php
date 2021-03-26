<?php

declare(strict_types=1);

namespace App\Controller\Pub\Secondary;

use App\Service\Listing\Secondary\RecentListingsService;
use App\Service\Setting\SettingsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RssController extends AbstractController
{
    /**
     * @Route("/rss", name="app_rss")
     */
    public function index(
        RecentListingsService $recentListingsService,
        SettingsService $settingsService
    ): Response {
        $settingsDto = $settingsService->getSettingsDto();

        return $this->render('secondary/rss.xml.twig', [
            'listingList' => $recentListingsService->getLatestListings(360),
            'settingsDto' => $settingsDto,
        ]);
    }
}
