<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\Admin;
use App\Entity\User;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class LoginUserProgrammaticallyService
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    public function __construct(
        TokenStorageInterface $tokenStorage,
        SessionInterface $session,
        RequestStack $requestStack,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->tokenStorage = $tokenStorage;
        $this->session = $session;
        $this->requestStack = $requestStack;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function loginUser(User $user): void
    {
        $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());

        $this->tokenStorage->setToken($token);
        $this->session->set('_security_main', \serialize($token));

        $event = new InteractiveLoginEvent($this->requestStack->getMasterRequest(), $token);
        $this->eventDispatcher->dispatch($event);
    }

    public function loginAdmin(Admin $admin): void
    {
        $token = new UsernamePasswordToken($admin, null, 'admin', $admin->getRoles());

        $this->tokenStorage->setToken($token);
        $this->session->set('_security_admin', \serialize($token));

        $event = new InteractiveLoginEvent($this->requestStack->getMasterRequest(), $token);
        $this->eventDispatcher->dispatch($event);
    }
}
