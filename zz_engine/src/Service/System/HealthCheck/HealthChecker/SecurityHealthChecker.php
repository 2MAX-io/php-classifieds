<?php

declare(strict_types=1);

namespace App\Service\System\HealthCheck\HealthChecker;

use App\Helper\ExceptionHelper;
use App\Helper\FilePath;
use App\Helper\Str;
use App\Service\System\HealthCheck\Base\HealthCheckerInterface;
use App\Service\System\HealthCheck\HealthCheckResultDto;
use App\System\Cache\AppCacheEnum;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Psr\Log\LoggerInterface;
use Psr\SimpleCache\CacheInterface;
use Symfony\Component\HttpFoundation\UrlHelper;
use Symfony\Contracts\Translation\TranslatorInterface;
use Webmozart\PathUtil\Path;

class SecurityHealthChecker implements HealthCheckerInterface
{
    /**
     * @var TranslatorInterface
     */
    private $trans;

    /**
     * @var CacheInterface
     */
    private $cache;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var UrlHelper
     */
    private $urlHelper;

    public function __construct(
        TranslatorInterface $trans,
        UrlHelper $urlHelper,
        CacheInterface $cache,
        LoggerInterface $logger
    ) {
        $this->trans = $trans;
        $this->cache = $cache;
        $this->logger = $logger;
        $this->urlHelper = $urlHelper;
    }

    public function checkHealth(): HealthCheckResultDto
    {
        if ($this->cache->get(AppCacheEnum::ADMIN_SECURITY_CHECK, false)) {
            return new HealthCheckResultDto(false);
        }

        $healthCheckResultDto = $this->engineNotPublic();
        if ($healthCheckResultDto) {
            return $healthCheckResultDto;
        }

        $healthCheckResultDto = $this->gitDirNotAccessible();
        if ($healthCheckResultDto) {
            return $healthCheckResultDto;
        }

        /**
         * cache only when security check successful
         */
        $this->cache->set(AppCacheEnum::ADMIN_SECURITY_CHECK, true, 3600*16);

        return new HealthCheckResultDto(false);
    }

    private function engineNotPublic(): ?HealthCheckResultDto
    {
        $testFilePath = Path::canonicalize(FilePath::getEngineDir() . '/zzzz_secuirty_check_file.html');
        if (!\file_exists($testFilePath)) {
            return new HealthCheckResultDto(true, $this->trans->trans('trans.ALERT! security can not be checked, required file not found: %file%', [
                '%file%' => $testFilePath,
            ]));
        }

        try {
            $client = new Client([
                RequestOptions::TIMEOUT => 10,
                RequestOptions::CONNECT_TIMEOUT => 10,
                RequestOptions::READ_TIMEOUT => 10,
                RequestOptions::VERIFY => false,
                RequestOptions::HTTP_ERRORS => false,
            ]);

            $testedUrl = $this->urlHelper->getAbsoluteUrl(Path::makeRelative($testFilePath, FilePath::getPublicDir()));
            $response = $client->get($testedUrl);

            if (Str::containsOneOf($response->getBody()->getContents(), ['zbD2vXzqDyiFqE2iqFPPM', 'SecurityHealthChecker'])) {
                return new HealthCheckResultDto(true, $this->trans->trans('trans.ALERT! security alert, not allowed file is publicly accessible %url%', [
                    '%url%' => $testedUrl,
                ]));
            }
        } catch (\Throwable $e) {
            $this->logger->critical('error during security check ' . __METHOD__, ExceptionHelper::flatten($e));

            return new HealthCheckResultDto(true, $this->trans->trans('trans.Unknown error during security audit'));
        }

        return null;
    }

    private function gitDirNotAccessible(): ?HealthCheckResultDto
    {
        try {
            $client = new Client([
                RequestOptions::TIMEOUT => 10,
                RequestOptions::CONNECT_TIMEOUT => 10,
                RequestOptions::READ_TIMEOUT => 10,
                RequestOptions::VERIFY => false,
                RequestOptions::HTTP_ERRORS => false,
            ]);

            $testedUrl = $this->urlHelper->getAbsoluteUrl('/.git/HEAD');
            $response = $client->get($testedUrl);

            if (Str::containsOneOf($response->getBody()->getContents(), ['ref:'])) {
                return new HealthCheckResultDto(true, $this->trans->trans('trans.ALERT! security alert, not allowed file is publicly accessible %url%', [
                    '%url%' => $testedUrl,
                ]));
            }
        } catch (\Throwable $e) {
            $this->logger->critical('error during security check ' . __METHOD__, ExceptionHelper::flatten($e));

            return new HealthCheckResultDto(true, $this->trans->trans('trans.Unknown error during security audit'));

        }

        return null;
    }
}
