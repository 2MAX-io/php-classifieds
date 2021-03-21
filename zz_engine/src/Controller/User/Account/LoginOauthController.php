<?php

declare(strict_types=1);

namespace App\Controller\User\Account;

use App\Controller\User\Base\AbstractUserController;
use App\Form\User\Account\Register\RegisterUserDto;
use App\Helper\ExceptionHelper;
use App\Helper\StringHelper;
use App\Repository\UserRepository;
use App\Security\LoginUserProgrammaticallyService;
use App\Security\UserChecker;
use App\Service\Setting\SettingsService;
use App\Service\System\FlashBag\FlashService;
use App\Service\User\Account\CreateUserService;
use Doctrine\ORM\EntityManagerInterface;
use Hybridauth\Hybridauth;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class LoginOauthController extends AbstractUserController
{
    public const GOOGLE_PROVIDER = 'Google';
    public const FACEBOOK_PROVIDER = 'Facebook';

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(UserRepository $userRepository, EntityManagerInterface $em)
    {
        $this->userRepository = $userRepository;
        $this->em = $em;
    }

    /**
     * @Route("/private/login/oauth/{provider}", name="app_login_oauth")
     */
    public function oauthLogin(
        Request $request,
        LoginUserProgrammaticallyService $loginUserProgrammaticallyService,
        CreateUserService $createUserService,
        UserChecker $userChecker,
        UrlGeneratorInterface $urlGenerator,
        SettingsService $settingsService,
        FlashService $flashService,
        LoggerInterface $logger
    ): Response {
        $oauthProviderName = $request->get('provider');
        $oauthProviderList = [];
        if ($settingsService->getSettingsDto()->getGoogleSignInClientSecret()) {
            $oauthProviderList[static::GOOGLE_PROVIDER] = [
                'enabled' => true,
                'keys' => [
                    'id' => $settingsService->getSettingsDto()->getGoogleSignInClientId(),
                    'secret' => $settingsService->getSettingsDto()->getGoogleSignInClientSecret(),
                ],
            ];
        }
        if ($settingsService->getSettingsDto()->getFacebookSignInAppSecret()) {
            $oauthProviderList[static::FACEBOOK_PROVIDER] = [
                'enabled' => true,
                'keys' => [
                    'id' => $settingsService->getSettingsDto()->getFacebookSignInAppId(),
                    'secret' => $settingsService->getSettingsDto()->getFacebookSignInAppSecret(),
                ],
            ];
        }
        $config = [
            'callback' => $urlGenerator->generate(
                'app_login_oauth',
                ['provider' => $oauthProviderName],
                UrlGeneratorInterface::ABSOLUTE_URL,
            ),
            'providers' => $oauthProviderList,
        ];

        try {
            $hybridAuth = new Hybridauth($config);
            $hybridAuth->disconnectAllAdapters();
            $authentication = $hybridAuth->authenticate($oauthProviderName);
            if ($authentication->isConnected()) {
                $userProfile = $authentication->getUserProfile();
                $email = StringHelper::emptyTrim($userProfile->emailVerified) ? $userProfile->email : $userProfile->emailVerified;
                if (null === $email) {
                    $logger->debug('could not find email address in oauth response');

                    return $this->render('security/login_oauth_no_email_error.html.twig');
                }

                $user = $this->userRepository->findOneBy(['email' => $email]);
                $userExists = null !== $user;
                if (!$userExists) {
                    $registerUserDto = new RegisterUserDto();
                    $registerUserDto->setEmail($email);
                    $user = $createUserService->registerUser($registerUserDto);
                    $user->setEnabled(true);
                    $this->em->flush();
                }
                $userChecker->checkPreAuth($user);
                $loginUserProgrammaticallyService->loginUser($user, $request);
            } else {
                $logger->error('user not connected with ouath provider, $authentication->isConnected() is false');
                $flashService->addFlash(FlashService::ERROR_ABOVE_FORM, 'trans.Sorry, could not login');
            }
            $authentication->disconnect();
        } catch (\Exception $e) {
            $logger->critical('error during oauth login', ExceptionHelper::flatten($e));
            $flashService->addFlash(FlashService::ERROR_ABOVE_FORM, 'trans.Sorry, could not login');
        }

        return $this->redirectToRoute('app_listing_new');
    }
}
