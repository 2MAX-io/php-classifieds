<?php

declare(strict_types=1);

namespace App\Controller\Pub;

use App\Helper\FilePath;
use App\System\Glide\AppServerFactory;
use Intervention\Image\Exception\NotFoundException;
use League\Glide\Responses\SymfonyResponseFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Webmozart\PathUtil\Path;

class ResizeImageController
{
    /**
     * @Route("/static/{path}/resized_{type}_{file}", name="app_resize_image", requirements={"path"=".+"})
     */
    public function index(Request $request, string $path, string $type, string $file): Response
    {
//        return new Response('image resize '. $path . '/' . $file);

        if (!in_array(Path::getExtension($file, true), ['jpg', 'png', 'gif', 'jpeg'])) {
            throw new NotFoundException();
        }

        $server = AppServerFactory::create([
            'source' => FilePath::getStaticPath(),
            'cache' => FilePath::getStaticPath(),
            'cache_path_prefix' => 'cache',
            'response' => new SymfonyResponseFactory($request)
        ]);

        $targetPath = Path::canonicalize(FilePath::getProjectDir() . $request->getRequestUri());

        if (!in_array(Path::getExtension($targetPath, true), ['jpg', 'png', 'gif', 'jpeg'])) {
            throw new NotFoundException();
        }

        $cachedPath = $server->makeImage($path . '/' . $file, $this->getParams($type));

        rename(Path::canonicalize(FilePath::getStaticPath() . '/' . $cachedPath), $targetPath);

        return $server->getResponseFactory()->create($server->getCache(), Path::makeRelative($targetPath, FilePath::getStaticPath()));
    }

    private function getParams(string $type): array
    {
        if ('list' === $type) {
            return ['w' => 180, 'h' => 180];
        }

        return ['w' => 300, 'h' => 400];
    }
}
