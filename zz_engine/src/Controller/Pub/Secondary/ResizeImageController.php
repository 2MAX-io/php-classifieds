<?php

declare(strict_types=1);

namespace App\Controller\Pub\Secondary;

use App\Helper\FileHelper;
use App\Helper\FilePath;
use App\Helper\IniHelper;
use App\Secondary\ImageManipulation\ImageManipulationFactory;
use App\Service\Setting\EnvironmentService;
use App\Service\System\Image\Dto\ImageDto;
use League\Glide\Responses\ResponseFactoryInterface;
use League\Glide\Responses\SymfonyResponseFactory;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Webmozart\PathUtil\Path;

class ResizeImageController
{
    public const TYPE_LIST = 'list';
    public const TYPE_NORMAL = 'normal';

    /**
     * @var EnvironmentService
     */
    private $environmentService;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(EnvironmentService $environmentService, LoggerInterface $logger)
    {
        $this->environmentService = $environmentService;
        $this->logger = $logger;
    }

    /**
     * @Route("/static/resized/{type}/{path}/{file}", name="app_resize_image", requirements={"path"=".+"})
     */
    public function resizeImage(
        Request $request,
        SessionInterface $session,
        string $path,
        string $type,
        string $file
    ): Response {
        if ($session->isStarted()) {
            $session->save();
        }
        IniHelper::setMemoryLimitIfLessThanMb(256);

        $requestUriWithoutGet = \strtok($request->getRequestUri(), '?');
        $sourcePath = $path.'/'.$file;
        $targetPath = Path::canonicalize(FilePath::getPublicDir().$requestUriWithoutGet);

        return $this->getImageResponse($request, $type, $sourcePath, $targetPath);
    }

    private function getImageResponse(Request $request, string $type, string $sourcePath, string $targetPath): Response
    {
        if (!FileHelper::isImage($sourcePath)) {
            $this->logger->debug('source path not image', [
                'sourcePath' => $sourcePath,
            ]);

            throw new NotFoundHttpException();
        }

        if (!FileHelper::isImage($targetPath)) {
            $this->logger->debug('target path not image', [
                'sourcePath' => $sourcePath,
            ]);

            throw new NotFoundHttpException();
        }

        if (!$this->isInsideStaticDir($targetPath)) {
            $this->logger->debug('target path not inside expected directory', [
                'sourcePath' => $sourcePath,
            ]);

            throw new NotFoundHttpException();
        }

        if (!$this->isInsideStaticDir(FilePath::getStaticPath().'/'.$sourcePath)) {
            $this->logger->debug('source path not inside expected directory', [
                'sourcePath' => $sourcePath,
            ]);

            throw new NotFoundHttpException();
        }

        if (!\file_exists(FilePath::getStaticPath().'/'.$sourcePath)) {
            if ($this->environmentService->getExternalImageUrlForDevelopment()) {
                return new RedirectResponse(
                    \rtrim($this->environmentService->getExternalImageUrlForDevelopment(), '/')
                    .'/'.$request->getRequestUri()
                );
            }
            $this->logger->debug('resized image not found for `{sourcePath}`, type: `{$type}`', [
                'type' => $type,
                'sourcePath' => $sourcePath,
            ]);

            return new RedirectResponse('/static/system/empty.png');
        }

        /**
         * -------------------------------------------------------------------------------------------------------------
         * Everything must be already valid and safe after this point
         * -------------------------------------------------------------------------------------------------------------
         */
        $imageDto = new ImageDto();
        $imageDto->setSourcePath(FilePath::getStaticPath().'/'.$sourcePath);
        $imageDto->updateSize();
        $imageDto->setType($type);
        $imageDto->setImageParams($this->getImageParams($imageDto));
        $imageWorker = ImageManipulationFactory::create(
            [
                'source' => FilePath::getStaticPath(),
                'cache' => FilePath::getStaticPath(),
                'cache_path_prefix' => Path::makeRelative(FilePath::getTempResizeImageCache(), FilePath::getStaticPath()),
                'response' => new SymfonyResponseFactory($request),
            ]
        );
        $cachedPath = $imageWorker->makeImage($sourcePath, $imageDto->getImageParams());
        $imageWorker->getSource()->rename(
            $cachedPath,
            Path::makeRelative($targetPath, FilePath::getStaticPath())
        );

        $responseFactory = $imageWorker->getResponseFactory();
        if (!$responseFactory instanceof ResponseFactoryInterface) {
            throw new \RuntimeException('could not create response factory');
        }

        return $responseFactory->create(
            $imageWorker->getCache(),
            Path::makeRelative($targetPath, FilePath::getStaticPath())
        );
    }

    /**
     * @return array<string,int|string>
     */
    private function getImageParams(ImageDto $imageDto): array
    {
        if (static::TYPE_LIST === $imageDto->getType()) {
            return [
                'w' => 480,
                'h' => 270,
                'fit' => 'max',
                'q' => 60,
            ];
        }

        if (static::TYPE_NORMAL === $imageDto->getType()) {
            return [
                'w' => 1920,
                'h' => 1080,
                'fit' => 'max',
                'q' => 75,
            ];
        }

        $this->logger->debug('image params not found for `{$type}`', [
            'type' => $imageDto->getType(),
            'sourcePath' => $imageDto->getSourcePath(),
        ]);

        throw new NotFoundHttpException();
    }

    private function isInsideStaticDir(string $path): bool
    {
        $longestCommonPath = Path::getLongestCommonBasePath([
            FilePath::getStaticPath(),
            Path::canonicalize($path),
        ]);

        return $longestCommonPath === FilePath::getStaticPath();
    }
}
