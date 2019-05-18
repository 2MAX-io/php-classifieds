<?php

declare(strict_types=1);

namespace App\Controller\Admin\Other;

use App\Service\Cron\CronService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CronController extends AbstractController
{
    /**
     * @Route("/cron/2g3Yd2fickgJAWPJ377Mp")
     */
    public function adminIndex(CronService $cronService, EntityManagerInterface $em): Response
    {
        $cronService->run();

        $em->flush();

        return new Response('ok');
    }
}
