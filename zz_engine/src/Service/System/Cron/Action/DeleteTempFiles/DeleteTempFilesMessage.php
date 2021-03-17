<?php

declare(strict_types=1);

namespace App\Service\System\Cron\Action\DeleteTempFiles;

use App\Service\System\Messenger\Base\OneAtTimeMessageInterface;

/**
 * @see CleanDatabaseCronService
 */
class DeleteTempFilesMessage implements OneAtTimeMessageInterface
{
}
