<?php

declare(strict_types=1);

namespace App\Service\System\Cron\Action\Base;

use App\Service\System\Cron\Dto\CronDto;

interface CronActionInterface
{
    public function runFromCron(CronDto $cronDto): void;
}
