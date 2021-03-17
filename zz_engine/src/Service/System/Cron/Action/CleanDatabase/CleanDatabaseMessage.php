<?php

declare(strict_types=1);

namespace App\Service\System\Cron\Action\CleanDatabase;

use App\Service\System\Messenger\Base\OneAtTimeMessageInterface;

/**
 * @see CleanDatabaseCronService
 */
class CleanDatabaseMessage implements OneAtTimeMessageInterface
{
}
