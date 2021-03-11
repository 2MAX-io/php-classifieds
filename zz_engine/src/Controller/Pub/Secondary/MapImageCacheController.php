<?php

declare(strict_types=1);

namespace App\Controller\Pub\Secondary;

use App\Helper\ExceptionHelper;
use App\Helper\FileHelper;
use App\Helper\FilePath;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class MapImageCacheController
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @Route(
     *      "/static/cache/map/{x}_{y}_{z}.png",
     *      name="app_map_image_cache",
     *      requirements={
     *          "z"="\d+",
     *          "x"="\d+",
     *          "y"="\d+",
     *      },
     *      options={"expose": true},
     * )
     * @Route(
     *      "/static/cache/map",
     *      name="app_map_image_cache_template",
     *      options={"expose": true},
     * )
     */
    public function resizeImage(
        SessionInterface $session,
        string $z,
        string $x,
        string $y
    ): Response {
        if ($session->isStarted()) {
            $session->save();
        }
        if (empty($_ENV['APP_ENABLE_MAP_CACHE'])) {
            throw new NotFoundHttpException();
        }

        $z = \basename($z);
        $x = \basename($x);
        $y = \basename($y);

        $mapCacheDirPath = FilePath::getStaticPath().'/cache/map';
        $destinationPath = $mapCacheDirPath.'/'.\basename("{$x}_{$y}_{$z}.png");
        FileHelper::throwExceptionIfUnsafeFilename($destinationPath);
        FileHelper::throwExceptionIfPathOutsideDir($destinationPath, $mapCacheDirPath);
        $mapUrl = "https://tile.openstreetmap.org/{$z}/{$x}/{$y}.png";
        $httpOptions = [
            'http' => [
                'method' => 'GET',
                'header' => 'User-Agent: Nominatim-Test',
            ],
        ];
        $streamContext = \stream_context_create($httpOptions);

        try {
            $imageData = \file_get_contents($mapUrl, false, $streamContext);
            if (!\file_exists($mapCacheDirPath)
                && !\mkdir($mapCacheDirPath, 0755, true)
                && !\is_dir($mapCacheDirPath)
            ) {
                throw new \RuntimeException(\sprintf('Directory "%s" was not created', $mapCacheDirPath));
            }
            \file_put_contents($destinationPath, $imageData);
        } catch (\Throwable $e) {
            $this->logger->error('could not load map file', ExceptionHelper::flatten($e));

            return new RedirectResponse($mapUrl);
        }

        return new Response($imageData);
    }
}
