<?php

declare(strict_types=1);

namespace App\Service\System\Upgrade\Api;

use App\Helper\Json;
use App\Helper\ExceptionHelper;
use App\Helper\Str;
use App\Service\System\Signature\SignatureVerifyNormalSecurity;
use App\Service\System\Upgrade\Base\UpgradeApi;
use App\Service\System\Upgrade\Dto\LatestVersionDto;
use App\System\Cache\RuntimeCacheEnum;
use App\Version;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Stream;
use GuzzleHttp\RequestOptions;
use Psr\Log\LoggerInterface;
use Symfony\Component\Cache\Simple\ArrayCache;
use Symfony\Component\HttpFoundation\Response;

class UpgradeApiService
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ArrayCache
     */
    private $arrayCache;

    public function __construct(ArrayCache $arrayCache, LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->arrayCache = $arrayCache;
    }

    public function getLatestVersion(): ?LatestVersionDto
    {
        $cacheName = RuntimeCacheEnum::LATEST_VERSION;
        if ($this->arrayCache->has($cacheName)) {
            return $this->arrayCache->get($cacheName);
        }

        $return = $this->getLatestVersionNoCache();
        $this->arrayCache->set($cacheName, $return);

        return $return;
    }

    public function getLatestVersionNoCache(): ?LatestVersionDto
    {
        try {
            $client = new Client([
                RequestOptions::TIMEOUT => 10,
                RequestOptions::CONNECT_TIMEOUT => 10,
                RequestOptions::READ_TIMEOUT => 10,
            ]);

            $request = new Request('GET', UpgradeApi::LATEST_VERSION_URL);

            $response = $client->send($request);

            if ($response->getStatusCode() === Response::HTTP_OK) {
                $responseBody = $response->getBody()->getContents();
                $signatureNormal = $response->getHeader(UpgradeApi::HEADER_SIGNATURE_BODY_NORMAL_SECURITY)[0] ?? null;

                if (null === $signatureNormal) {
                    $this->logger->error('missing signature', ['$responseBody' => $responseBody]);

                    return null;
                }

                if (!SignatureVerifyNormalSecurity::authenticate($responseBody, $signatureNormal)) {
                    $this->logger->error('invalid signature', ['$responseBody' => $responseBody]);

                    return null;
                }

                $responseArr = Json::decodeToArray($responseBody);
                $versionDto = new LatestVersionDto((int) ($responseArr['version']), (string) ($responseArr['date']));

                return $versionDto;
            }
        } catch (\Throwable $e) {
            $this->logger->error('failed to get current version from server', ExceptionHelper::flatten($e));
        }

        return null;
    }

    public function getUpgradeList(): ?array
    {
        try {
            $jsonArray = [
                'fromVersion' => Version::getVersion(),
                'url' => $_SERVER['HTTP_REFERER'] ?? '',
            ];

            $client = new Client([
                RequestOptions::TIMEOUT => 30,
                RequestOptions::CONNECT_TIMEOUT => 30,
                RequestOptions::READ_TIMEOUT => 30,
            ]);

            $request = new Request('POST', UpgradeApi::UPGRADE_LIST_URL);
            $request->withBody(new Stream(Str::toStream(Json::jsonEncode($jsonArray))));
            $response = $client->send($request);

            $responseBody = $response->getBody()->getContents();
            $signatureNormal = $response->getHeader(UpgradeApi::HEADER_SIGNATURE_BODY_NORMAL_SECURITY)[0] ?? null;
            if (null === $signatureNormal) {
                $this->logger->error('missing signature', ['$responseBody' => $responseBody]);

                return null;
            }
            if (!SignatureVerifyNormalSecurity::authenticate($responseBody, $signatureNormal)) {
                $this->logger->error('invalid signature', ['$responseBody' => $responseBody]);

                return null;
            }

            return Json::decodeToArray($responseBody);
        } catch (\Throwable $e) {
            $this->logger->error('failed when getting upgrade', ExceptionHelper::flatten($e));
        }

        return null;
    }
}
