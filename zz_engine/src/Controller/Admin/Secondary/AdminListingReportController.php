<?php

declare(strict_types=1);

namespace App\Controller\Admin\Secondary;

use App\Controller\Admin\Base\AbstractAdminController;
use App\Entity\System\ListingReport;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;

class AdminListingReportController extends AbstractAdminController
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/admin/red5/listing-report", name="app_admin_listing_report_list", methods={"GET"})
     */
    public function listingReport(): Response
    {
        $this->denyUnlessAdmin();

        /** @var ListingReport[] $listingReportList */
        $listingReportList = $this->em->getRepository(ListingReport::class)->findBy(
            ['removed' => false],
            ['id' => Criteria::DESC],
            360,
        );

        return $this->render('admin/secondary/listing_report/listing_report_list.html.twig', [
            'listingReportList' => $listingReportList,
        ]);
    }

    /**
     * @Route("/admin/red5/listing-report/{id}/remove-listing-report", name="app_admin_listing_report_remove", methods={"DELETE"})
     */
    public function remove(Request $request, ListingReport $listingReport): Response
    {
        $this->denyUnlessAdmin();

        if (!$this->isCsrfTokenValid('csrf_listingReportRemove'.$listingReport->getId(), $request->request->get('_token'))) {
            throw new InvalidCsrfTokenException('token not valid');
        }
        $listingReport->setRemoved(true);
        $this->em->flush();

        return $this->redirectToRoute('app_admin_listing_report_list');
    }
}
