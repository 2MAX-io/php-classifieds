<?php

declare(strict_types=1);

namespace App\Controller\User\Listing;

use App\Controller\User\Base\AbstractUserController;
use App\Entity\Listing;
use App\Service\System\Routing\RefererService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;

class ListingActionForUserController extends AbstractUserController
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
     * @Route("/user/listing/{id}/remove", name="app_user_listing_remove", methods={"DELETE"})
     */
    public function removeListing(
        Request $request,
        Listing $listing,
        RefererService $refererService
    ): Response {
        $this->dennyUnlessCurrentUserAllowed($listing, true);

        if (!$this->isCsrfTokenValid('csrf_remove'.$listing->getId(), $request->request->get('_token'))) {
            throw new InvalidCsrfTokenException('token not valid');
        }
        $listing->setUserRemoved(true);
        $this->em->flush();

        if ($refererService->isRefererUrlFromRouteName('app_user_listing_edit')) {
            return $this->redirectToRoute('app_user_listing_list');
        }

        return $refererService->redirectToRefererResponse();
    }

    /**
     * @Route("/user/listing/deactivate/{id}", name="app_user_listing_deactivate", methods={"PATCH"})
     */
    public function deactivateListing(Request $request, Listing $listing, RefererService $refererService): Response
    {
        $this->dennyUnlessCurrentUserAllowed($listing);

        if (!$this->isCsrfTokenValid('csrf_deactivate'.$listing->getId(), $request->request->get('_token'))) {
            throw new InvalidCsrfTokenException('token not valid');
        }
        $listing->setUserDeactivated(true);
        $this->em->flush();

        return $refererService->redirectToRefererResponse();
    }

    /**
     * @Route("/user/listing/activate/{id}", name="app_user_listing_activate", methods={"PATCH"})
     */
    public function activateListing(Request $request, Listing $listing, RefererService $refererService): Response
    {
        $this->dennyUnlessCurrentUserAllowed($listing);

        if (!$this->isCsrfTokenValid('csrf_activate'.$listing->getId(), $request->request->get('_token'))) {
            throw new InvalidCsrfTokenException('token not valid');
        }
        $listing->setUserDeactivated(false);
        $this->em->flush();

        /*
         * after activation, listing could be expired, for better ux experience, in that case redirect to
         * listing validity extend controller
         */
        if ($listing->getStatus() === $listing::STATUS_EXPIRED) {
            return $this->redirectToRoute('app_user_extend_expiration', ['id' => $listing->getId()]);
        }

        return $refererService->redirectToRefererResponse();
    }
}
