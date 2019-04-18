<?php

declare(strict_types=1);

namespace App\Controller\Pub;

use App\Helper\FilePath;
use App\System\Glide\AppServerFactory;
use League\Glide\Responses\SymfonyResponseFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Webmozart\PathUtil\Path;

class ResizeImageController
{
    /**
     * @Route("/static/{path}/size_{type}_{file}", name="app_resize_image", requirements={"path"=".+"})
     */
    public function index(Request $request, string $path, string $type, string $file): Response
    {
        ini_set('memory_limit','256M'); // todo: check if can reduce it

        if (!in_array(Path::getExtension($file, true), ['jpg', 'png', 'gif', 'jpeg'], true)) {
            throw new NotFoundHttpException();
        }

        if (!in_array(Path::getExtension($request->getRequestUri(), true), ['jpg', 'png', 'gif', 'jpeg'], true)) {
            throw new NotFoundHttpException();
        }

        $sourcePath = $path . '/' . $file;
        $targetPath = Path::canonicalize(FilePath::getProjectDir() . $request->getRequestUri());

        if (!in_array(Path::getExtension($targetPath, true), ['jpg', 'png', 'gif', 'jpeg'], true)) {
            throw new NotFoundHttpException();
        }

        if (Path::getLongestCommonBasePath([FilePath::getStaticPath(), $targetPath]) !== FilePath::getStaticPath()) {
            // path not inside expected directory
            throw new NotFoundHttpException();
        }

        if (!in_array(Path::getExtension($sourcePath, true), ['jpg', 'png', 'gif', 'jpeg'], true)) {
            throw new NotFoundHttpException();
        }

        if (Path::getLongestCommonBasePath([FilePath::getStaticPath(), FilePath::getStaticPath().'/'.$sourcePath]) !== FilePath::getStaticPath()) {
            // path not inside expected directory
            throw new NotFoundHttpException();
        }

        /**
         * Everything must be already valid and safe after this point
         */

        $server = AppServerFactory::create([
            'source' => FilePath::getStaticPath(),
            'cache' => FilePath::getStaticPath(),
            'cache_path_prefix' => 'cache',
            'response' => new SymfonyResponseFactory($request)
        ]);
        $cachedPath = $server->makeImage($sourcePath, $this->getParams($type));

        rename(FilePath::getStaticPath() . '/' . $cachedPath, $targetPath);

        return $server->getResponseFactory()->create($server->getCache(), Path::makeRelative($targetPath, FilePath::getStaticPath()));
    }

    private function getParams(string $type): array
    {
        if ('list' === $type) {
            return ['w' => 180, 'h' => 180];
        }

        if ('normal' === $type) {
            return ['w' => 1920, 'h' => 1080];
        }

        throw new NotFoundHttpException();
    }
}
