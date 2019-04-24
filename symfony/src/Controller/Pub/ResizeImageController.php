<?php

declare(strict_types=1);

namespace App\Controller\Pub;

use App\Helper\File;
use App\Helper\FilePath;
use App\System\Glide\AppServerFactory;
use League\Glide\Responses\SymfonyResponseFactory;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Webmozart\PathUtil\Path;

class ResizeImageController
{
    /**
     * @Route("/static/resized/{type}/{path}/{file}", name="app_resize_image", requirements={"path"=".+"})
     */
    public function index(Request $request, string $path, string $type, string $file): Response
    {
        ini_set('memory_limit','256M'); // todo: check if can reduce it

        if (!File::isImage($file)) {
            throw new NotFoundHttpException();
        }

        if (!File::isImage($request->getRequestUri())) {
            throw new NotFoundHttpException();
        }

        $sourcePath = $path . '/' . $file;
        $targetPath = Path::canonicalize(FilePath::getProjectDir() . $request->getRequestUri());

        return $this->getResponse($request, $type, $sourcePath, $targetPath);
    }

    private function getParams(string $type): array
    {
        if ('list' === $type) {
            return ['w' => 260, 'h' => 200, 'fit' => 'max',];
        }

        if ('normal' === $type) {
            return ['w' => 1920, 'h' => 1080, 'fit' => 'max',];
        }

        throw new NotFoundHttpException();
    }

    private function getResponse(Request $request, string $type, string $sourcePath, string $targetPath): Response
    {
        if (!File::isImage($targetPath)) {
            throw new NotFoundHttpException();
        }

        if (Path::getLongestCommonBasePath([FilePath::getStaticPath(), $targetPath]) !== FilePath::getStaticPath()) {
            // path not inside expected directory
            throw new NotFoundHttpException();
        }

        if (!File::isImage($sourcePath)) {
            throw new NotFoundHttpException();
        }

        if (Path::getLongestCommonBasePath([FilePath::getStaticPath(), FilePath::getStaticPath().'/'.$sourcePath]) !== FilePath::getStaticPath()) {
            // path not inside expected directory
            throw new NotFoundHttpException();
        }

        if (!file_exists(FilePath::getStaticPath() . '/' . $sourcePath)) {
            return new RedirectResponse('/static/system/empty.png');
        }

        /**
         * -------------------------------------------------------------------------------------------------------------
         * Everything must be already valid and safe after this point
         * -------------------------------------------------------------------------------------------------------------
         */
        $server = AppServerFactory::create(
            [
                'source' => FilePath::getStaticPath(),
                'cache' => FilePath::getStaticPath(),
                'cache_path_prefix' => 'cache',
                'response' => new SymfonyResponseFactory($request)
            ]
        );
        $cachedPath = $server->makeImage($sourcePath, $this->getParams($type));

        $server->getSource()->rename(
            $cachedPath,
            Path::makeRelative($targetPath, FilePath::getStaticPath())
        );

        return $server->getResponseFactory()->create(
            $server->getCache(),
            Path::makeRelative($targetPath, FilePath::getStaticPath())
        );
    }
}
