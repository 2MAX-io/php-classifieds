<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\Admin\Base\AbstractAdminController;
use App\Exception\UserVisibleException;
use App\Helper\ArrayHelper;
use App\Service\Admin\Listing\ListingActivateService;
use App\Service\Admin\ListingAction\ListingActionService;
use App\Service\System\Routing\RefererService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;

class AdminListingActivateController extends AbstractAdminController
{
    public const ACTIVATE_ACTION = 'ACTIVATE_ACTION';
    public const REJECT_ACTION = 'REJECT_ACTION';
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/admin/red5/listing/activate/list", name="app_admin_listing_activate_list")
     */
    public function listingActivateListForAdmin(
        Request $request,
        ListingActivateService $listingActivateService
    ): Response {
        $this->denyUnlessAdmin();

        $adminListingListDto = $listingActivateService->getAwaitingActivationList(
            (int) $request->query->get('page', 1)
        );

        return $this->render('admin/listing/listing_activate.html.twig', [
            'listings' => $adminListingListDto->getResults(),
            'pager' => $adminListingListDto->getPager(),
        ]);
    }

    /**
     * @Route("/admin/red5/listing/activate/list/action-on-selected", name="app_admin_listing_activate_action_on_selected")
     */
    public function actionForSelected(
        Request $request,
        ListingActionService $listingActionService,
        RefererService $refererService
    ): Response {
        $this->denyUnlessAdmin();

        if (!$this->isCsrfTokenValid('csrf_activateSelectedListings', $request->get('_token'))) {
            throw new InvalidCsrfTokenException('token not valid');
        }

        $action = $request->get('action');
        if (empty($action)) {
            throw new UserVisibleException('Action not found');
        }
        $listingIds = \explode(',', $request->get('selected'));

        if ($action === static::ACTIVATE_ACTION) {
            $listingActionService->activate(ArrayHelper::valuesToInt($listingIds));
        }

        if ($action === static::REJECT_ACTION) {
            $listingActionService->reject(ArrayHelper::valuesToInt($listingIds));
        }

        $this->em->flush();

        return $refererService->redirectToRefererResponse();
    }
}
