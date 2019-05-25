<?php

declare(strict_types=1);

namespace App\Service\System\HealthCheck\HealthChecker;

use App\Helper\FilePath;
use App\Service\System\HealthCheck\Base\HealthCheckerInterface;
use App\Service\System\HealthCheck\HealthCheckResultDto;
use Symfony\Contracts\Translation\TranslatorInterface;

class InstallDirRemovedHealthChecker implements HealthCheckerInterface
{
    /**
     * @var TranslatorInterface
     */
    private $trans;

    public function __construct(TranslatorInterface $trans)
    {
        $this->trans = $trans;
    }

    public function checkHealth(): HealthCheckResultDto
    {
        if (\file_exists(FilePath::getPublicDir() . '/install')) {
            return new HealthCheckResultDto(true, $this->trans->trans('trans.Install directory has not been removed'));
        }

        return new HealthCheckResultDto(false);
    }
}
