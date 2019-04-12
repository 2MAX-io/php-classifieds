<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Service\Cron\CronService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CronController extends AbstractController
{
    /**
     * @Route("/cron/2g3Yd2fickgJAWPJ377Mp")
     */
    public function adminIndex(CronService $cronService): Response
    {
        $cronService->run();

        return new Response('ok');
    }
}
