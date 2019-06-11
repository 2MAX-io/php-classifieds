<?php

declare(strict_types=1);

namespace App\Service\System\Routing;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\UrlHelper;
use Symfony\Component\Routing\RouterInterface;
use Webmozart\PathUtil\Url;

class RefererService
{
    /**
     * @var UrlHelper
     */
    private $urlHelper;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(UrlHelper $urlHelper, RequestStack $requestStack, RouterInterface $router, LoggerInterface $logger)
    {
        $this->urlHelper = $urlHelper;
        $this->requestStack = $requestStack;
        $this->router = $router;
        $this->logger = $logger;
    }

    public function redirectToRefererResponse(): Response
    {
        return new RedirectResponse($this->getSafeRefererUrl());
    }

    public function getSafeRefererUrl(): string
    {
        $referer = $this->getRelativeReferer();

        if (!$this->routeForRefererFound()) {
            $this->logger->error('route for referer URL not found', [
                'referer' => $referer,
            ]);

            return $this->router->generate('app_index');
        }

        return $referer;
    }

    public function routeForRefererFound(): bool
    {
        return $this->getRouteNameFromReferer() !== null;
    }

    public function getRelativeReferer(): string
    {
        $referer = $this->requestStack->getMasterRequest()->headers->get('referer');

        return $this->getRelativeUrl($referer);
    }

    public function getRelativeUrl(string $url): string
    {
        return '/' . Url::makeRelative($url, $this->urlHelper->getAbsoluteUrl('/'));
    }

    public function getRouteNameFromReferer(): ?string
    {
        $this->router->getContext()->setMethod(Request::METHOD_GET);
        $routeArray = $this->router->match($this->getRelativeReferer());

        return $routeArray['_route'] ?? null;
    }

    /**
     * @param string $route #Route
     * @return bool
     */
    public function refererIsRoute(string $route): bool
    {
        return $this->getRouteNameFromReferer() === $route;
    }
}
