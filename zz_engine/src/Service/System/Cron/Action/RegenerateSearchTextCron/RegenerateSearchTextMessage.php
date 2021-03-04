<?php

declare(strict_types=1);

namespace App\Service\System\Cron\Action\RegenerateSearchTextCron;

use App\Service\System\Messenger\Base\OneAtTimeMessageInterface;

/**
 * @see RegenerateSearchTextCronService
 */
class RegenerateSearchTextMessage implements OneAtTimeMessageInterface
{
}
