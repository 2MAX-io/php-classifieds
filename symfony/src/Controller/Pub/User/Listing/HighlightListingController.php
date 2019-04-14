<?php

declare(strict_types=1);

namespace App\Controller\Pub\User\Listing;

use App\Controller\Pub\User\Base\AbstractUserController;
use App\Entity\Listing;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HighlightListingController extends AbstractUserController
{
    /**
     * @Route("/user/highlight/{id}", name="app_user_highlight_listing")
     */
    public function highlight(Listing $listing): Response
    {
        $this->dennyUnlessCurrentUserListing($listing);

        return $this->render('user/listing/highlight_extend.html.twig', [
        ]);
    }
}
