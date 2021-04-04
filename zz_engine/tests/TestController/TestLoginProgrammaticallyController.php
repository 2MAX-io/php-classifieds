<?php

declare(strict_types=1);

namespace App\Tests\TestController;

use App\Entity\System\Admin;
use App\Exception\UserVisibleException;
use App\Repository\UserRepository;
use App\Security\LoginUserProgrammaticallyService;
use App\Tests\Enum\TestUserLoginEnum;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestLoginProgrammaticallyController extends AbstractController
{
    /**
     * @var LoginUserProgrammaticallyService
     */
    private $loginUserProgrammaticallyService;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(
        LoginUserProgrammaticallyService $loginUserProgrammaticallyService,
        UserRepository $userRepository,
        EntityManagerInterface $em
    ) {
        $this->loginUserProgrammaticallyService = $loginUserProgrammaticallyService;
        $this->userRepository = $userRepository;
        $this->em = $em;
    }

    /**
     * @Route("/zzzz/test/{urlSecret}/login-programmatically-user", name="app_test_login_programmatically_user")
     */
    public function testProgrammaticallyLoginUser(Request $request, string $urlSecret): Response
    {
        if (!isset($_ENV['APP_NOT_PUBLIC_URL_SECRET'])) {
            throw new UserVisibleException('ENV APP_NOT_PUBLIC_URL_SECRET not found');
        }
        if ($urlSecret !== $_ENV['APP_NOT_PUBLIC_URL_SECRET']) {
            throw new UserVisibleException('urlSecret not correct');
        }

        $this->loginUserProgrammaticallyService->loginUser($this->userRepository->findOneBy([
            'email' => TestUserLoginEnum::LOGIN,
        ]), $request);

        return new Response('ok');
    }

    /**
     * @Route("/zzzz/test/{urlSecret}/login-programmatically-admin", name="app_test_login_programmatically_admin")
     */
    public function testProgrammaticallyLoginAdmin(Request $request, string $urlSecret): Response
    {
        if (!isset($_ENV['APP_NOT_PUBLIC_URL_SECRET'])) {
            throw new UserVisibleException('ENV APP_NOT_PUBLIC_URL_SECRET not found');
        }
        if ($urlSecret !== $_ENV['APP_NOT_PUBLIC_URL_SECRET']) {
            throw new UserVisibleException('urlSecret not correct');
        }

        $this->loginUserProgrammaticallyService->loginAdmin($this->em->getRepository(Admin::class)->findOneBy([
            'email' => TestUserLoginEnum::LOGIN_ADMIN,
        ]), $request);

        return new Response('ok');
    }
}
