<?php

declare(strict_types=1);

namespace App\System\Messenger;

use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;

class MessengerHelperService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function countMessages(string $class): int
    {
        return $this->countMessagesContaining(\addslashes(\addslashes($class)));
    }

    public function countMessagesContaining(string $bodyContains): int
    {
        /** @var \PDO $pdo */
        $pdo = $this->em->getConnection();
        $stmt = $pdo->prepare(<<<EOT
            SELECT COUNT(1)
            FROM zzzz_messenger_messages
            WHERE true 
            && body LIKE :body
            && delivered_at IS NULL
            && queue_name != 'failed'
            && created_at > :after_datetime
EOT);
        $stmt->bindValue(':body', '%'.$bodyContains.'%');
        $stmt->bindValue(':after_datetime', Carbon::now()->subHours(6));
        $stmt->execute();

        return (int) $stmt->fetchColumn();
    }
}
