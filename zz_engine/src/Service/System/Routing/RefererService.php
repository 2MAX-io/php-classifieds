<?php

declare(strict_types=1);

namespace App\Service\System\Routing;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

class RefererService
{
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

    public function __construct(
        RequestStack $requestStack,
        RouterInterface $router,
        LoggerInterface $logger
    ) {
        $this->requestStack = $requestStack;
        $this->router = $router;
        $this->logger = $logger;
    }

    public function redirectToRefererResponse(): Response
    {
        return new RedirectResponse($this->getSafeRefererUrl());
    }

    /**
     * checks if previous page route name got by referer url equals
     */
    public function isRefererUrlFromRouteName(string $routeName): bool
    {
        return $this->getRouteNameFromReferer() === $routeName;
    }

    public function routeNameForRefererUrlFound(): bool
    {
        return null !== $this->getRouteNameFromReferer();
    }

    /**
     * @param string $routeWhenNoReferer #Route
     */
    public function getSafeRefererUrl(string $routeWhenNoReferer = 'app_index'): string
    {
        $referer = $this->requestStack->getMasterRequest()->headers->get('referer');
        if (null === $referer) {
            return $this->router->generate($routeWhenNoReferer);
        }

        if (!$this->routeNameForRefererUrlFound()) {
            $this->logger->error('route for referer URL not found', [
                'referer' => $referer,
            ]);

            return $this->router->generate('app_index');
        }

        return $referer;
    }

    public function getRouteNameFromReferer(): ?string
    {
        $this->router->getContext()->setMethod(Request::METHOD_GET);
        $relativeRefererUrl = $this->getRelativeRefererUrl();
        if (null === $relativeRefererUrl) {
            return null;
        }
        $routeArray = $this->router->match($relativeRefererUrl);

        return $routeArray['_route'] ?? null;
    }

    public function getRelativeRefererUrl(): ?string
    {
        $referer = $this->requestStack->getMasterRequest()->headers->get('referer');
        if (null === $referer) {
            return null;
        }

        $url = \parse_url($referer, \PHP_URL_PATH);
        if (!\is_string($url)) {
            throw new \RuntimeException("can not generate path from referer: `{$referer}`");
        }

        return $url;
    }
}
