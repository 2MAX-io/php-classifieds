<?php

declare(strict_types=1);

namespace App\Controller\Pub\Secondary;

use App\Service\Listing\Secondary\RecentListingsService;
use App\Service\Setting\SettingsService;
use Laminas\Feed\Writer\Feed;
use Laminas\Feed\Writer\Writer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class RssController extends AbstractController
{
    /**
     * @Route("/rss", name="app_rss")
     */
    public function index(
        RecentListingsService $recentListingsService,
        UrlGeneratorInterface $urlGenerator,
        SettingsService $settingsService
    ): Response {
        $settingsDto = $settingsService->getSettingsDto();

        $feed = new Feed();
        $feed->setTitle($settingsDto->getRssTitle());
        $feed->setDescription($settingsDto->getRssDescription());
        $feed->setLink($urlGenerator->generate('app_index', [], UrlGeneratorInterface::ABSOLUTE_URL));
        $feed->setGenerator('Mk1BWC5pbyBDbGFzc2lmaWVkIEFkcw');

        foreach ($recentListingsService->getLatestListings(360) as $listing) {
            $link = $urlGenerator->generate(
                'app_listing_show',
                ['id' => $listing->getId(), 'slug' => $listing->getSlug()],
                UrlGeneratorInterface::ABSOLUTE_URL,
            );

            $entry = $feed->createEntry();
            $entry->setTitle($listing->getTitle());
            $entry->setLink($link);
            $entry->setDateModified($listing->getLastEditDate());
            $entry->setDateCreated($listing->getFirstCreatedDate());
            $entry->setDescription($listing->getDescription());
            $entry->setContent($listing->getDescription());
            $feed->addEntry($entry);
        }

        return new Response($feed->export(Writer::TYPE_RSS_ANY));
    }
}
