<?php

declare(strict_types=1);

namespace App\Controller\Pub\Secondary;

use App\Entity\Listing;
use App\Entity\System\ListingReport;
use App\Form\Secondary\ListingReportType;
use App\Helper\DateHelper;
use App\Security\CurrentUserService;
use App\Service\System\FlashBag\FlashService;
use App\Service\System\SystemLog\PoliceLog\PoliceLogHelperService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReportListingController extends AbstractController
{
    /**
     * @var CurrentUserService
     */
    private $currentUserService;

    /**
     * @var PoliceLogHelperService
     */
    private $policeLogHelperService;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var FlashService
     */
    private $flashService;

    public function __construct(
        CurrentUserService $currentUserService,
        PoliceLogHelperService $policeLogHelperService,
        FlashService $flashService,
        EntityManagerInterface $em
    ) {
        $this->currentUserService = $currentUserService;
        $this->policeLogHelperService = $policeLogHelperService;
        $this->flashService = $flashService;
        $this->em = $em;
    }

    /**
     * @Route("/private/listing/{listing}/report-listing", name="app_report_listing")
     */
    public function reportListingForAbuse(Request $request, Listing $listing): Response
    {
        $listingReport = new ListingReport();
        $user = $this->currentUserService->getUserOrNull();
        if ($user) {
            $listingReport->setUser($this->currentUserService->getUserOrNull());
            $listingReport->setEmail($user->getEmail());
        }
        $listingReport->setDatetime(DateHelper::create());
        $listingReport->setListing($listing);
        $listingReport->setPoliceLog($this->policeLogHelperService->getShortLogString());
        $form = $this->createForm(ListingReportType::class, $listingReport);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($listingReport);
            $this->em->flush();

            $this->flashService->addFlash(
                FlashService::SUCCESS_ABOVE_FORM,
                'trans.Your report has been send',
            );

            return $this->redirectToRoute($request->get('_route'), [
                'listing' => $listing->getId(),
            ]);
        }

        return $this->render('secondary/report_listing.html.twig', [
            'listing' => $listing,
            'form' => $form->createView(),
        ]);
    }
}
