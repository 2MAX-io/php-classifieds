<?php

declare(strict_types=1);

namespace App\Controller\User\Message;

use App\Controller\User\Base\AbstractUserController;
use App\Entity\Listing;
use App\Entity\UserMessage;
use App\Form\User\Message\Dto\SendUserMessageDto;
use App\Form\User\Message\SendUserMessageType;
use App\Security\CurrentUserService;
use App\Service\User\Message\UserMessageListService;
use App\Service\User\Message\UserMessageSendService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Routing\Annotation\Route;

class UserMessageController extends AbstractUserController
{
    /**
     * @Route("/user/message/list/{userMessage}", name="app_user_message_list")
     */
    public function userMessageList(
        Request $request,
        UserMessageListService $userMessageListService,
        UserMessageSendService $userMessageSendService,
        CurrentUserService $currentUserService,
        UserMessage $userMessage = null
    ): Response {
        $this->dennyUnlessUser();

        $listing = $userMessage ? $userMessage->getListing() : null;

        $sendUserMessageDto = new SendUserMessageDto();
        $sendUserMessageDto->setListing($listing);
        $sendUserMessageDto->setUserMessage($userMessage);
        $sendUserMessageDto->setCurrentUser($currentUserService->getUser());
        if ($sendUserMessageDto->getUserMessage() && !$userMessageSendService->allowedToSendMessage($sendUserMessageDto)) {
            throw new UnauthorizedHttpException('user can not send this message');
        }

        $form = $this->createForm(SendUserMessageType::class, $sendUserMessageDto);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $userMessageSendService->sendMessage($sendUserMessageDto);

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute($request->get('_route'), ['userMessage' => $userMessage->getId()]);
        }

        $messageList = [];
        if ($listing) {
            $messageList = $userMessageListService->getMessageListForUser(
                $listing,
                $sendUserMessageDto->getCurrentUser()
            );
        }

        return $this->render('user/message/user_message.html.twig', [
            'messageList' => $messageList,
            'threadList' => $userMessageListService->getThreadsForUser($sendUserMessageDto->getCurrentUser()),
            'currentUser' => $sendUserMessageDto->getCurrentUser(),
            'currentListing' => $listing,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/user/message/send-message-to-user/{listing}", name="app_user_message_send")
     */
    public function sendMessageToUser(
        Request $request,
        Listing $listing,
        UserMessageListService $userMessageListService,
        UserMessageSendService $userMessageSendService,
        CurrentUserService $currentUserService
    ): Response {
        $this->dennyUnlessUser();

        $sendUserMessageDto = new SendUserMessageDto();
        $sendUserMessageDto->setListing($listing);
        $sendUserMessageDto->setCurrentUser($currentUserService->getUser());

        $form = $this->createForm(SendUserMessageType::class, $sendUserMessageDto);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $userMessageSendService->sendMessage($sendUserMessageDto);

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute($request->get('_route'), ['listing' => $listing->getId()]);
        }

        return $this->render('user/message/user_message.html.twig', [
            'messageList' => $userMessageListService->getMessageListForUser(
                $listing,
                $sendUserMessageDto->getCurrentUser()
            ),
            'threadList' => $userMessageListService->getThreadsForUser($sendUserMessageDto->getCurrentUser()),
            'currentUser' => $sendUserMessageDto->getCurrentUser(),
            'currentListing' => $listing,
            'sendingFirstMessage' => true,
            'form' => $form->createView(),
        ]);
    }
}
