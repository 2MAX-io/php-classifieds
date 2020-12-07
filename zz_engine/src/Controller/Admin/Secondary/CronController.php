<?php

declare(strict_types=1);

namespace App\Controller\Admin\Secondary;

use App\Service\Cron\CronService;
use App\Service\Cron\Dto\CronMainDto;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CronController extends AbstractController
{
    /**
     * @Route("/private/cron/2g3Yd2fickgJAWPJ377Mp")
     */
    public function adminIndex(CronService $cronService, EntityManagerInterface $em): Response
    {
        $cronMainDto = new CronMainDto();
        $cronService->run($cronMainDto);

        $em->flush();

        return new Response('ok');
    }
}
