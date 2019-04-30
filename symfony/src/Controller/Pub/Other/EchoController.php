<?php

declare(strict_types=1);

namespace App\Controller\Pub\Other;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EchoController extends AbstractController
{
    /**
     * @Route("/echo")
     */
    public function echo(): Response
    {
        return new Response('echo');
    }
}
