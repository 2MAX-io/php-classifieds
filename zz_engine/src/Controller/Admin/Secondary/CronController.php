<?php

declare(strict_types=1);

namespace App\Controller\Admin\Secondary;

use App\Exception\UserVisibleException;
use App\Service\System\Cron\CronService;
use App\Service\System\Cron\Dto\CronDto;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CronController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * default:
     * /zzzz/cron/2g3Yd2fickgJAWPJ377Mp/cron-main
     *
     * @Route("/zzzz/cron/{urlSecret}/cron-main")
     */
    public function cronMain(string $urlSecret, CronService $cronService): Response
    {
        if (!isset($_ENV['APP_NOT_PUBLIC_URL_SECRET'])) {
            throw new UserVisibleException('ENV APP_NOT_PUBLIC_URL_SECRET not found');
        }
        if ($urlSecret !== $_ENV['APP_NOT_PUBLIC_URL_SECRET']) {
            throw new UserVisibleException('urlSecret not correct');
        }

        $cronMainDto = new CronDto();
        $cronService->run($cronMainDto);

        $this->em->flush();

        return new Response('ok');
    }
}
