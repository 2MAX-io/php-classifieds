<?php

declare(strict_types=1);

namespace App\Controller\Pub;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ResizeImageController
{
    /**
     * @Route("/static/{path}", name="app_resize_image", requirements={"path"=".+"})
     */
    public function index(string $path): Response
    {
        return new Response('image resize '. $path);
    }
}
