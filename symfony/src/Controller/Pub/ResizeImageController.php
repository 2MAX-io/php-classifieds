<?php

declare(strict_types=1);

namespace App\Controller\Pub;

use App\Helper\FilePath;
use App\System\Glide\AppServerFactory;
use League\Glide\Responses\SymfonyResponseFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ResizeImageController
{
    /**
     * @Route("/static/{path}/resized_{type}_{file}", name="app_resize_image", requirements={"path"=".+"})
     */
    public function index(Request $request, string $path, string $type, string $file): Response
    {
//        return new Response('image resize '. $path . '/' . $file);

        $server = AppServerFactory::create([
            'source' => FilePath::getStaticPath(),
            'cache' => FilePath::getStaticCachePath(),
            'response' => new SymfonyResponseFactory($request)
        ]);

        return $server->getImageResponse($path . '/' . $file, $this->getParams($type));
    }

    private function getParams(string $type): array
    {
        if ('list' === $type) {
            return ['w' => 180, 'h' => 180];
        }

        return ['w' => 300, 'h' => 400];
    }
}
