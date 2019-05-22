<?php

declare(strict_types=1);

namespace App\Service\System\Upgrade;

use App\Helper\Json;
use App\Helper\Str;
use App\Service\System\Upgrade\Dto\NewestVersionDto;
use App\Service\System\Upgrade\Dto\UpgradeResponseDto;
use App\Version;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Stream;
use GuzzleHttp\RequestOptions;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;

class UpgradeApiService
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function getVersion(): ?NewestVersionDto
    {
        try {
            $client = new Client([
                RequestOptions::TIMEOUT => 10,
                RequestOptions::CONNECT_TIMEOUT => 10,
                RequestOptions::READ_TIMEOUT => 10,
            ]);

            $request = new Request('GET', UpgradeApi::CURRENT_VERSION_URL);

            $response = $client->send($request);

            if ($response->getStatusCode() === Response::HTTP_OK) {
                $responseArr = Json::decodeToArray($response->getBody()->getContents());

                $responseArr['version'];

                $versionDto = new NewestVersionDto((int) ($responseArr['version']), (string) ($responseArr['date']));

                return $versionDto;
            }
        } catch (\Throwable $e) {
            $this->logger->error('failed to get current version from server', [$e->getMessage(), $e]);
        }

        return null;
    }

    public function getUpgrade(): ?array
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

            $request = new Request('POST', UpgradeApi::UPGRADE_URL);
            $request->withBody(new Stream(Str::toStream(Json::jsonEncode($jsonArray))));
            $response = $client->send($request);

            $content = $response->getBody()->getContents();
            return Json::decodeToArray($content);
        } catch (\Throwable $e) {
            $this->logger->error('failed when getting upgrade', [$e->getMessage(), $e]);
        }

        return null;
    }
}
