<?php

declare(strict_types=1);

namespace App\Controller\User\Account;

use App\Controller\User\Base\AbstractUserController;
use App\Entity\User;
use App\Helper\ExceptionHelper;
use App\Helper\Str;
use App\Security\LoginUserProgrammaticallyService;
use App\Service\FlashBag\FlashService;
use App\Service\Setting\SettingsService;
use App\Service\User\Account\CreateUserService;
use Doctrine\ORM\EntityManagerInterface;
use Hybridauth\Hybridauth;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;

class LoginOauthController extends AbstractUserController
{
    public const GOOGLE_PROVIDER = 'Google';
    public const FACEBOOK_PROVIDER = 'Facebook';

    /**
     * @Route("/login/oauth/{provider}", name="app_login_oauth")
     */
    public function oauthLogin(
        Request $request,
        CreateUserService $createUserService,
        LoginUserProgrammaticallyService $loginUserProgrammaticallyService,
        UrlGeneratorInterface $urlGenerator,
        SettingsService $settingsService,
        EntityManagerInterface $em,
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
                UrlGeneratorInterface::ABSOLUTE_URL
            ),
            'providers' => $oauthProviderList,
        ];

        try{
            $hybridAuth = new Hybridauth($config);
            $adapter = $hybridAuth->authenticate($oauthProviderName);
            if (!$adapter->isConnected()) {
                throw new CustomUserMessageAuthenticationException('User is not connected.');
            }
            $userProfile = $adapter->getUserProfile();
            $email = Str::emptyTrim($userProfile->emailVerified) ? $userProfile->email : $userProfile->emailVerified;
            $user = $em->getRepository(User::class)->findOneBy(['email' => $email]);
            if ($user === null) {
                $user = $createUserService->registerUser($email);
                $user->setEnabled(true);
                $em->flush();
            }
            $loginUserProgrammaticallyService->loginUser($user);
            $adapter->disconnect();
        }
        catch(\Exception $e){
            $logger->critical('error during oauth login', ExceptionHelper::flatten($e));
            $flashService->addFlash(FlashService::ERROR_ABOVE_FORM, 'trans.Sorry, could not login');
        }

        return $this->redirectToRoute('app_listing_new');
    }
}
