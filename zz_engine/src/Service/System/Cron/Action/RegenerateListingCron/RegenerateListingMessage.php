<?php

declare(strict_types=1);

namespace App\Service\System\Cron\Action\RegenerateListingCron;

use App\Service\System\Messenger\Base\OneAtTimeMessageInterface;

/**
 * @see RegenerateListingCronService
 */
class RegenerateListingMessage implements OneAtTimeMessageInterface
{
}
