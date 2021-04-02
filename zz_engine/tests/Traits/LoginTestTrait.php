<?php

declare(strict_types=1);

namespace App\Tests\Traits;

use App\Entity\System\Admin;
use App\Entity\User;
use App\Tests\Enum\TestLoginEnum;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

trait LoginTestTrait
{
    public function loginUser(KernelBrowser $client, string $userEmail = TestLoginEnum::LOGIN): void
    {
        $session = self::$container->get('session');

        /**
         * @psalm-suppress UndefinedClass
         */
        $user = static::$kernel->getContainer()->get(EntityManagerInterface::class)->getRepository(User::class)->findOneBy([
            'email' => $userEmail,
        ]);

        $firewallName = 'main';
        // if you don't define multiple connected firewalls, the context defaults to the firewall name
        // See https://symfony.com/doc/current/reference/configuration/security.html#firewall-context
        $firewallContext = 'main';

        // you may need to use a different token class depending on your application.
        // for example, when using Guard authentication you must instantiate PostAuthenticationGuardToken
        $token = new UsernamePasswordToken($user, null, $firewallName, $user->getRoles());
        $session->set('_security_'.$firewallContext, \serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $client->getCookieJar()->set($cookie);
    }

    public function loginAdmin(KernelBrowser $client): void
    {
        $session = self::$container->get('session');

        $user = static::$kernel->getContainer()->get(EntityManagerInterface::class)->getRepository(Admin::class)->findOneBy([]);

        $firewallName = 'admin';
        // if you don't define multiple connected firewalls, the context defaults to the firewall name
        // See https://symfony.com/doc/current/reference/configuration/security.html#firewall-context
        $firewallContext = 'admin';

        // you may need to use a different token class depending on your application.
        // for example, when using Guard authentication you must instantiate PostAuthenticationGuardToken
        $token = new UsernamePasswordToken($user, null, $firewallName, $user->getRoles());
        $session->set('_security_'.$firewallContext, \serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $client->getCookieJar()->set($cookie);
    }
}
