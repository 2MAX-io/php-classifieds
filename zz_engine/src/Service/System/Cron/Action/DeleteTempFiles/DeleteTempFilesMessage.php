<?php

declare(strict_types=1);

namespace App\Service\System\Cron\Action\DeleteTempFiles;

use App\Service\System\Messenger\Base\OneAtTimeMessageInterface;

/**
 * @see DeleteTempFilesCronService
 */
class DeleteTempFilesMessage implements OneAtTimeMessageInterface
{
}
