<?php

declare(strict_types=1);

namespace App\Service\System\Cron\Action\DeleteExpiredListingFiles;

use App\Service\System\Messenger\Base\OneAtTimeMessageInterface;

/**
 * @see DeleteExpiredListingFilesCronService
 */
class DeleteExpiredListingFilesMessage implements OneAtTimeMessageInterface
{
}
