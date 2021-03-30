<?php

declare(strict_types=1);

namespace App\Tests\Smoke;

use App\Entity\System\Admin;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

/**
 * @internal
 * @coversNothing
 */
class AdminIndexPageTest extends WebTestCase
{
    /**
     * @var KernelBrowser
     */
    private $client;

    public function testIndexPage(): void
    {
        $this->client = static::createClient();
        $this->loginAdmin();
        $url = $this->getRouter()->generate('app_admin_index');

        $this->client->request('GET', $url);
        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    protected function getRouter(): RouterInterface
    {
        return static::$kernel->getContainer()->get('router');
    }

    private function loginAdmin(): void
    {
        $session = self::$container->get('session');

        // somehow fetch the user (e.g. using the user repository)
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
        $this->client->getCookieJar()->set($cookie);
    }
}
