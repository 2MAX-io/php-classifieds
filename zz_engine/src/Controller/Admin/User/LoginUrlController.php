<?php

declare(strict_types=1);

namespace App\Controller\Admin\User;

use App\Controller\Admin\Base\AbstractAdminController;
use App\Entity\Admin;
use App\Exception\UserVisibleException;
use App\Security\LoginUserProgrammaticallyService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class LoginUrlController extends AbstractAdminController
{
    /**
     * @Route("/admin/login-url/{adminLoginUrlSecret}}/login-url", name="app_admin_login_url", methods={"GET"})
     */
    public function adminLoginUtl(
        string $adminLoginUrlSecret,
        Request $request,
        LoginUserProgrammaticallyService $loginUserProgrammaticallyService,
        EntityManagerInterface $em,
        LoggerInterface $logger
    ): Response {
        if (!isset($_ENV['APP_ADMIN_LOGIN_URL_SECRET'])) {
            $logger->critical('ENV variable APP_ADMIN_LOGIN_URL_SECRET not found');
            throw new UserVisibleException('ENV variable APP_ADMIN_LOGIN_URL_SECRET not found');
        }
        if (!isset($_ENV['APP_ADMIN_LOGIN_HASH_SECRET'])) {
            $logger->critical('ENV variable APP_ADMIN_LOGIN_HASH_SECRET not found');
            throw new UserVisibleException('ENV variable APP_ADMIN_LOGIN_HASH_SECRET not found');
        }
        if ($adminLoginUrlSecret !== $_ENV['APP_ADMIN_LOGIN_URL_SECRET']) {
            $logger->critical('{adminLoginUrlSecret} does not match ENV APP_ADMIN_LOGIN_URL_SECRET');
            throw new UserVisibleException('{adminLoginUrlSecret} does not match ENV APP_ADMIN_LOGIN_URL_SECRET');
        }

        $secret = $_ENV['APP_ADMIN_LOGIN_HASH_SECRET'];
        $time = $request->get('time');
        $hash = $request->get('hash');
        $unique = $request->get('unique');
        $userEmail = $request->get('userEmail');
        if (\abs(\time() - $time) > 3600*24) {
            throw new UserVisibleException('login link has expired, go to previous page, refresh it, and try again by clicking link');
        }

        if (\strlen($unique) < 32) {
            throw new UserVisibleException('unique is too short');
        }

        $generatedHash = \sha1(\sha1((string) $time) . $secret . $unique . $userEmail);
        if ($hash === $generatedHash) {
            /** @var Admin $admin */
            $admin = $em->getRepository(Admin::class)->findOneBy(['email' => $userEmail]);
            if (null === $admin) {
                throw new UserVisibleException('user not found');
            }

            $loginUserProgrammaticallyService->loginAdmin($admin);

            return $this->redirectToRoute('app_admin_listing_activate_list');
        }

        throw new UserVisibleException('login url incorrect, go to previous page, refresh it, and try again by clicking link');
    }

    /**
     * @Route("/admin/red5/administrator-user/login-url-generate", name="app_admin_administrator_login_url_generate")
     */
    public function administratorLoginUrlGenerate(
        UrlGeneratorInterface $urlGenerator,
        EntityManagerInterface $em,
        LoggerInterface $logger
    ): Response
    {
        $this->denyUnlessAdmin();

        if (!isset($_ENV['APP_ADMIN_LOGIN_URL_SECRET'])) {
            $logger->critical('ENV variable APP_ADMIN_LOGIN_URL_SECRET not found');
            throw new UserVisibleException('ENV variable APP_ADMIN_LOGIN_URL_SECRET not found');
        }
        if (!isset($_ENV['APP_ADMIN_LOGIN_HASH_SECRET'])) {
            $logger->critical('ENV variable APP_ADMIN_LOGIN_HASH_SECRET not found');
            throw new UserVisibleException('ENV variable APP_ADMIN_LOGIN_HASH_SECRET not found');
        }

        $admin = $em->getRepository(Admin::class)->findOneBy([], ['id' => 'DESC']);
        if (!$admin instanceof Admin) {
            $logger->critical('admin user not found');
            throw new UserVisibleException('admin user not found');
        }

        $userEmail = $admin->getEmail();
        $secret = $_ENV['APP_ADMIN_LOGIN_HASH_SECRET'];
        $time = \time();
        $unique = \sha1(\uniqid('classified_login_unique_', true));

        $loginUrl = $urlGenerator->generate(
            'app_admin_login_url',
            [
                'adminLoginUrlSecret' => $_ENV['APP_ADMIN_LOGIN_URL_SECRET'],
                'time' => $time,
                'unique' => $unique,
                'userEmail' => $userEmail,
                'hash' => \sha1(\sha1((string) $time) . $secret . $unique . $userEmail),
            ],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        return new Response($loginUrl);
    }
}
