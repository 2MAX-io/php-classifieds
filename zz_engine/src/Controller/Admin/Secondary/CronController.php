<?php

declare(strict_types=1);

namespace App\Controller\Admin\Secondary;

use App\Exception\UserVisibleException;
use App\Service\System\Cron\CronService;
use App\Service\System\Cron\Dto\CronDto;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;

class CronController extends AbstractController
{
    /**
     * @var CronService
     */
    private $cronService;

    /**
     * @var KernelInterface
     */
    private $kernel;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(CronService $cronService, KernelInterface $kernel, EntityManagerInterface $em)
    {
        $this->cronService = $cronService;
        $this->kernel = $kernel;
        $this->em = $em;
    }

    /**
     * default:
     * /zzzz/cron/2g3Yd2fickgJAWPJ377Mp/cron-main
     *
     * @Route("/zzzz/cron/{urlSecret}/cron-main", name="app_cron")
     */
    public function cronMain(string $urlSecret): Response
    {
        if (!isset($_ENV['APP_NOT_PUBLIC_URL_SECRET'])) {
            throw new UserVisibleException('ENV APP_NOT_PUBLIC_URL_SECRET not found');
        }
        if ($urlSecret !== $_ENV['APP_NOT_PUBLIC_URL_SECRET']) {
            throw new UserVisibleException('urlSecret not correct');
        }

        $cronMainDto = new CronDto();
        $this->cronService->run($cronMainDto);
        $this->em->flush();

        $application = new Application($this->kernel);
        $application->setAutoExit(false);

        $input = new ArrayInput([
            'command' => 'messenger:consume',
            'receivers' => ['one_at_time'],
            '--memory-limit' => '128M',
            '--limit' => '1',
            '--time-limit' => 5,
            '--quiet' => '--quiet',
        ]);
        $application->run($input);

        $input = new ArrayInput([
            'command' => 'messenger:consume',
            'receivers' => ['async'],
            '--time-limit' => 20,
            '--memory-limit' => '128M',
            '--quiet' => '--quiet',
        ]);
        $application->run($input);

        return new Response('ok');
    }
}
