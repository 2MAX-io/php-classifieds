<?php

declare(strict_types=1);

namespace App\Controller\Pub;

use App\Helper\FileHelper;
use App\Helper\FilePath;
use App\System\ImageManipulation\ImageManipulationFactory;
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
        \ini_set('memory_limit','256M'); // todo: check if can reduce it

        $requestUriWithoutGet = \strtok($request->getRequestUri(), '?');

        $sourcePath = $path . '/' . $file;
        $targetPath = Path::canonicalize(FilePath::getProjectDir() . $requestUriWithoutGet);

        return $this->getResponse($request, $type, $sourcePath, $targetPath);
    }

    private function getResponse(Request $request, string $type, string $sourcePath, string $targetPath): Response
    {
        if (!FileHelper::isImage($sourcePath)) {
            throw new NotFoundHttpException();
        }

        if (!FileHelper::isImage($targetPath)) {
            throw new NotFoundHttpException();
        }

        if (!$this->isInsideStaticDir($targetPath)) {
            // path not inside expected directory
            throw new NotFoundHttpException();
        }

        if (!$this->isInsideStaticDir(FilePath::getStaticPath().'/'.$sourcePath)) {
            // path not inside expected directory
            throw new NotFoundHttpException();
        }

        if (!\file_exists(FilePath::getStaticPath() . '/' . $sourcePath)) {
            return new RedirectResponse('/static/system/empty.png');
        }

        /**
         * -------------------------------------------------------------------------------------------------------------
         * Everything must be already valid and safe after this point
         * -------------------------------------------------------------------------------------------------------------
         */
        $server = ImageManipulationFactory::create(
            [
                'source' => FilePath::getStaticPath(),
                'cache' => FilePath::getStaticPath(),
                'cache_path_prefix' => 'cache',
                'response' => new SymfonyResponseFactory($request)
            ]
        );
        $cachedPath = $server->makeImage($sourcePath, $this->getMakeImageParams($type));

        $server->getSource()->rename(
            $cachedPath,
            Path::makeRelative($targetPath, FilePath::getStaticPath())
        );

        return $server->getResponseFactory()->create(
            $server->getCache(),
            Path::makeRelative($targetPath, FilePath::getStaticPath())
        );
    }

    private function getMakeImageParams(string $type): array
    {
        if ('list' === $type) {
            return ['w' => 260, 'h' => 200, 'fit' => 'max',];
        }

        if ('normal' === $type) {
            return ['w' => 1920, 'h' => 1080, 'fit' => 'max',];
        }

        throw new NotFoundHttpException();
    }

    private function isInsideStaticDir(string $path): bool
    {
        return Path::getLongestCommonBasePath(
                [FilePath::getStaticPath(), Path::canonicalize($path)]
            ) === FilePath::getStaticPath();
    }
}
