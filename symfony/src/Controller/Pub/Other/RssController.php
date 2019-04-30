<?php

declare(strict_types=1);

namespace App\Controller\Pub\Other;

use App\Service\Listing\Helper\ListingHelperService;
use App\Service\Setting\SettingsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Zend\Feed\Writer\Feed;
use Zend\Feed\Writer\Writer;

class RssController extends AbstractController
{
    /**
     * @Route("/rss", name="app_rss")
     */
    public function index(
        ListingHelperService $listingHelperService,
        UrlGeneratorInterface $urlGenerator,
        SettingsService $settingsService
    ): Response {
        $settingsDto = $settingsService->getHydratedSettingsDto();

        $feed = new Feed;
        $feed->setTitle($settingsDto->getRssTitle());
        $feed->setDescription($settingsDto->getRssDescription());
        $feed->setLink($urlGenerator->generate('app_index'));

        foreach ($listingHelperService->getLatestListings(100) as $listing) {
            $link = $urlGenerator->generate(
                'app_listing_show',
                ['id' => $listing->getId(), 'slug' => $listing->getSlug()],
                UrlGeneratorInterface::ABSOLUTE_URL
            );

            $entry = $feed->createEntry();
            $entry->setTitle($listing->getTitle());
            $entry->setLink($link);
            $entry->setDateModified($listing->getLastEditDate());
            $entry->setDateCreated($listing->getFirstCreatedDate());
            $entry->setDescription($listing->getDescription());
            $entry->setContent($listing->getDescription());
            $feed->addEntry($entry);        }

        return new Response($feed->export(Writer::TYPE_RSS_ANY));
    }
}
