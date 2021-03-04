<?php

declare(strict_types=1);

namespace App\Controller\Admin\Security;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AdminSecurityController extends AbstractController
{
    /**
     * @Route("/admin/red5/login", name="app_admin_login")
     */
    public function loginForAdmin(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        /** @var AuthenticationException $error */
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/admin/admin_login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }
}
