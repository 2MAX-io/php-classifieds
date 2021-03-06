<?php

declare(strict_types=1);

namespace App\Controller\User\Security;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function loginForUser(Request $request, AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        /** @var AuthenticationException $error */
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
        if (empty($lastUsername)) {
            $lastUsername = $request->get('username');
        }

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    /**
     * @Route("/logout", name="app_logout", methods={"GET"})
     */
    public function logout(): void
    {
        // controller can be blank: it will never be executed!
        throw new \RuntimeException('Don\'t forget to activate logout in security.yaml');
    }

    /**
     * @Route("/logout/confirm-logout", name="app_logout_confirm", methods={"GET"})
     */
    public function confirmLogout(): Response
    {
        return $this->render('security/logout_confirm.html.twig');
    }
}
