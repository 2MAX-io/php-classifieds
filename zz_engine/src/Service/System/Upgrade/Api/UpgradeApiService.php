<?php

declare(strict_types=1);

namespace App\Service\System\Upgrade\Api;

use App\Enum\RuntimeCacheEnum;
use App\Helper\ExceptionHelper;
use App\Helper\JsonHelper;
use App\Service\System\Cache\RuntimeCacheService;
use App\Service\System\License\LicenseService;
use App\Service\System\Signature\VerifySignature;
use App\Service\System\Upgrade\Base\UpgradeApi;
use App\Service\System\Upgrade\Dto\LatestVersionDto;
use App\Version;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\RequestOptions;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;

class UpgradeApiService
{
    /**
     * @var LicenseService
     */
    private $licenseService;

    /**
     * @var RuntimeCacheService
     */
    private $runtimeCache;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        LicenseService $licenseService,
        RuntimeCacheService $runtimeCache,
        LoggerInterface $logger
    ) {
        $this->licenseService = $licenseService;
        $this->runtimeCache = $runtimeCache;
        $this->logger = $logger;
    }

    public function getLatestVersion(): ?LatestVersionDto
    {
        return $this->runtimeCache->get(RuntimeCacheEnum::LATEST_VERSION, function () {
            return $this->getLatestVersionNoCache();
        });
    }

    public function getLatestVersionNoCache(): ?LatestVersionDto
    {
        try {
            $client = new Client([
                RequestOptions::TIMEOUT => 10,
                RequestOptions::CONNECT_TIMEOUT => 5,
                RequestOptions::READ_TIMEOUT => 10,
            ]);
            $request = new Request(
                'GET',
                UpgradeApi::LATEST_VERSION_URL,
                [
                    'x-license' => \base64_encode($this->licenseService->getLicenseText()),
                    'x-license-url' => $this->licenseService->getCurrentUrlOfLicense(),
                ]
            );
            $response = $client->send($request);
            if (Response::HTTP_OK === $response->getStatusCode()) {
                $responseBody = $response->getBody()->getContents();
                $signature = $response->getHeader(UpgradeApi::HEADER_SIGNATURE)[0] ?? null;
                if (null === $signature) {
                    $this->logger->error('missing signature', ['$responseBody' => $responseBody]);

                    return null;
                }

                if (!VerifySignature::authenticate($responseBody, $signature)) {
                    $this->logger->error('invalid signature', ['$responseBody' => $responseBody]);

                    return null;
                }

                $responseArr = JsonHelper::toArray($responseBody);

                return new LatestVersionDto((int) $responseArr['version'], (string) $responseArr['date']);
            }
        } catch (\Throwable $e) {
            $this->logger->error('failed to get current version from server', ExceptionHelper::flatten($e));
        }

        return null;
    }

    /**
     * @return null|array<string,mixed>
     */
    public function getUpgradeList(): ?array
    {
        try {
            $jsonArray = [
                'forVersion' => Version::getVersion(),
            ];

            $client = new Client([
                RequestOptions::TIMEOUT => 30,
                RequestOptions::CONNECT_TIMEOUT => 30,
                RequestOptions::READ_TIMEOUT => 30,
            ]);

            $request = new Request(
                'POST',
                UpgradeApi::UPGRADE_LIST_URL,
                [
                    'x-license' => \base64_encode($this->licenseService->getLicenseText()),
                    'x-license-url' => $this->licenseService->getCurrentUrlOfLicense(),
                ],
                JsonHelper::toString($jsonArray)
            );
            $response = $client->send($request);
            $responseBody = $response->getBody()->getContents();
            $signature = $response->getHeader(UpgradeApi::HEADER_SIGNATURE)[0] ?? null;
            if (null === $signature) {
                $this->logger->error('missing signature', ['$responseBody' => $responseBody]);

                return null;
            }
            if (!VerifySignature::authenticate($responseBody, $signature)) {
                $this->logger->error('invalid signature', ['$responseBody' => $responseBody]);

                return null;
            }

            return JsonHelper::toArray($responseBody);
        } catch (\Throwable $e) {
            $this->logger->error('failed when getting upgrade', ExceptionHelper::flatten($e));
        }

        return null;
    }
}
