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
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\RequestOptions;
use Psr\Log\LoggerInterface;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Contracts\Cache\CacheInterface;
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
     * @var CacheInterface|AdapterInterface
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
        $cacheItem = $this->cache->getItem(AppCacheEnum::ADMIN_SECURITY_CHECK);
        if ($cacheItem->isHit() && $cacheItem->get() === true) {
            return new HealthCheckResultDto(false); // return no problems
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
        $cacheItem->expiresAfter(3600*16);
        $cacheItem->set(true);
        $this->cache->save($cacheItem);

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
                RequestOptions::TIMEOUT => 5,
                RequestOptions::CONNECT_TIMEOUT => 5,
                RequestOptions::READ_TIMEOUT => 5,
                RequestOptions::VERIFY => false,
                RequestOptions::HTTP_ERRORS => false,
            ]);

            $testedUrl = $this->urlHelper->getAbsoluteUrl('/' . Path::makeRelative($testFilePath, FilePath::getPublicDir()));
            $response = $client->get($testedUrl);

            if (Str::containsOneOf($response->getBody()->getContents(), ['zbD2vXzqDyiFqE2iqFPPM', 'SecurityHealthChecker'])) {
                return new HealthCheckResultDto(
                    true,
                    $this->trans->trans(
                        'trans.ALERT! security alert, not allowed file is publicly accessible %url%',
                        [
                            '%url%' => $testedUrl,
                        ]
                    )
                );
            }
        } catch (ConnectException $e) {
            $this->logger->critical('error during security check ' . __METHOD__, ExceptionHelper::flatten($e));

            return new HealthCheckResultDto(
                true,
                $this->trans->trans(
                    'trans.Could not connect to: %url%, during security audit, if testing on local machine you can ignore this',
                    [
                        '%url%' => $testedUrl ?? '',
                    ]
                )
            );
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
                RequestOptions::TIMEOUT => 5,
                RequestOptions::CONNECT_TIMEOUT => 5,
                RequestOptions::READ_TIMEOUT => 5,
                RequestOptions::VERIFY => false,
                RequestOptions::HTTP_ERRORS => false,
            ]);

            $testedUrl = $this->urlHelper->getAbsoluteUrl('/.git/HEAD');
            $response = $client->get($testedUrl);

            if (Str::containsOneOf($response->getBody()->getContents(), ['ref:'])) {
                return new HealthCheckResultDto(
                    true,
                    $this->trans->trans(
                        'trans.ALERT! security alert, not allowed file is publicly accessible %url%',
                        [
                            '%url%' => $testedUrl,
                        ]
                    )
                );
            }
        } catch (ConnectException $e) {
            $this->logger->critical('error during security check ' . __METHOD__, ExceptionHelper::flatten($e));

            return new HealthCheckResultDto(
                true,
                $this->trans->trans(
                    'trans.Could not connect to: %url%, during security audit, if testing on local machine you can ignore this',
                    [
                        '%url%' => $testedUrl ?? '',
                    ]
                )
            );
        } catch (\Throwable $e) {
            $this->logger->critical('error during security check ' . __METHOD__, ExceptionHelper::flatten($e));

            return new HealthCheckResultDto(true, $this->trans->trans('trans.Unknown error during security audit'));

        }

        return null;
    }
}
