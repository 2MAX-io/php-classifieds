<?php

declare(strict_types=1);

namespace App\Controller\User\Message;

use App\Controller\User\Base\AbstractUserController;
use App\Entity\Listing;
use App\Entity\UserMessageThread;
use App\Form\User\Message\Dto\SendUserMessageDto;
use App\Form\User\Message\SendUserMessageType;
use App\Security\CurrentUserService;
use App\Service\User\Message\UserMessageListService;
use App\Service\User\Message\UserMessageSendService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Routing\Annotation\Route;

class UserMessageController extends AbstractUserController
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
     * @Route("/user/message/list/", name="app_user_message_list")
     * @Route("/user/message/list/thread/{userMessageThread}", name="app_user_message_list_thread")
     */
    public function userMessageList(
        Request $request,
        UserMessageSendService $userMessageSendService,
        UserMessageListService $userMessageListService,
        CurrentUserService $currentUserService,
        UserMessageThread $userMessageThread = null
    ): Response {
        $this->dennyUnlessUser();

        /** @var Listing|null $listing */
        $listing = $userMessageThread ? $userMessageThread->getListing() : null;
        $currentUser = $currentUserService->getUser();

        $sendUserMessageDto = new SendUserMessageDto();
        $sendUserMessageDto->setListing($listing);
        $sendUserMessageDto->setUserMessageThread($userMessageThread);
        $sendUserMessageDto->setCurrentUser($currentUser);
        if ($userMessageThread && !$userMessageSendService->allowedToSendMessage($sendUserMessageDto)) {
            throw new UnauthorizedHttpException('user can not send this message');
        }

        $form = $this->createForm(SendUserMessageType::class, $sendUserMessageDto);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $userMessageSendService->sendMessage($sendUserMessageDto);
            $this->em->flush();

            return $this->redirectToRoute($request->get('_route'), [
                'userMessageThread' => $userMessageThread->getId(),
            ]);
        }

        $messageList = [];
        if ($userMessageThread) {
            $messageList = $userMessageListService->getMessageListForThread($userMessageThread);
            $userMessageListService->markReadByRecipient($messageList);
            $this->em->flush();
        }

        return $this->render('user/message/user_message.html.twig', [
            'threadList' => $userMessageListService->getThreadList($currentUser),
            'messageList' => $messageList,
            'currentUser' => $currentUser,
            'listing' => $listing,
            'userMessageThread' => $userMessageThread,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/user/message/respond-to-listing/{listing}", name="app_user_message_respond_to_listing")
     */
    public function respondToListing(
        Request $request,
        Listing $listing,
        UserMessageSendService $userMessageSendService,
        UserMessageListService $userMessageListService,
        CurrentUserService $currentUserService
    ): Response {
        $this->dennyUnlessUser();
        $currentUser = $currentUserService->getUser();

        $isCurrentUserListingOwner = $listing->getUserNotNull()->getId() === $currentUser->getId();
        if ($isCurrentUserListingOwner) {
            return $this->render('user/message/error/message_to_yourself_error.html.twig');
        }

        $threadExist = $userMessageListService->getExistingUserThreadForListing($listing, $currentUser);
        if ($threadExist) {
            return $this->redirectToRoute('app_user_message_list_thread', [
                'userMessageThread' => $threadExist->getId(),
            ]);
        }

        $sendUserMessageDto = new SendUserMessageDto();
        $sendUserMessageDto->setCreateThread(true);
        $sendUserMessageDto->setListing($listing);
        $sendUserMessageDto->setCurrentUser($currentUser);

        $form = $this->createForm(SendUserMessageType::class, $sendUserMessageDto);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $userMessageSendService->sendMessage($sendUserMessageDto);
            $this->em->flush();

            return $this->redirectToRoute('app_user_message_list_thread', [
                'userMessageThread' => $sendUserMessageDto->getUserMessageThreadNotNull()->getId(),
            ]);
        }

        return $this->render('user/message/reply_to_listing.html.twig', [
            'listing' => $listing,
            'form' => $form->createView(),
        ]);
    }
}
