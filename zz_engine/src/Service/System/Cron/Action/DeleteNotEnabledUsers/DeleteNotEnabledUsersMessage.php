<?php

declare(strict_types=1);

namespace App\Service\System\Cron\Action\DeleteNotEnabledUsers;

use App\Service\System\Messenger\Base\OneAtTimeMessageInterface;

/**
 * @see DeleteNotEnabledUsersCronService
 */
class DeleteNotEnabledUsersMessage implements OneAtTimeMessageInterface
{
}
