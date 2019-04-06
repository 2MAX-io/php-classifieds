<?php

declare(strict_types=1);

namespace App\Controller\Admin\Secondary\PoliceLog;

use App\Helper\IntegerHelper;
use App\Service\Listing\Secondary\PoliceLog\Dto\PoliceLogForUserMessageDto;
use App\Service\Listing\Secondary\PoliceLog\Dto\PoliceLogUserMessageItemDto;
use App\Service\Listing\Secondary\PoliceLog\PoliceLogForUserMessageService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PoliceLogForUserMessageController extends AbstractController
{
    /**
     * @var PoliceLogForUserMessageService
     */
    private $policeLogForUserMessageService;

    public function __construct(PoliceLogForUserMessageService $policeLogForUserMessageService)
    {
        $this->policeLogForUserMessageService = $policeLogForUserMessageService;
    }

    /**
     * @Route("/admin/red5/police-log/user-message", name="app_admin_police_log_user_message")
     */
    public function policeLogForUserMessage(Request $request): Response
    {
        $policeLogForUserMessageDto = new PoliceLogForUserMessageDto();
        $policeLogForUserMessageDto->setUserId(IntegerHelper::toInteger($request->get('user')));
        $policeLogForUserMessageDto->setListingId(IntegerHelper::toInteger($request->get('listing')));
        $policeLogForUserMessageDto->setThreadId(IntegerHelper::toInteger($request->get('thread')));
        $policeLogForUserMessageDto->setQuery($request->get('query'));
        $policeLogForUserMessageDto->setPage((int) $request->get('page', 1));
        $paginationDto = $this->policeLogForUserMessageService->getList($policeLogForUserMessageDto);
        /** @var PoliceLogUserMessageItemDto[] $policeLogUserMessages */
        $policeLogUserMessages = $paginationDto->getResults();

        return $this->render('admin/secondary/police_log/police_log_user_message.html.twig', [
            'policeLogUserMessages' => $policeLogUserMessages,
            'pager' => $paginationDto->getPager(),
        ]);
    }
}
