<?php

declare(strict_types=1);

namespace App\Service\Listing\Search;

use App\Entity\SearchHistory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class SaveSearchHistoryService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    public function __construct(EntityManagerInterface $em, EventDispatcherInterface $eventDispatcher)
    {
        $this->em = $em;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function saveSearch(string $query): void
    {
        $query = \trim($query);
        if (empty($query) || \strlen($query) < 2) {
            return;
        }

        $this->eventDispatcher->addListener(
            KernelEvents::TERMINATE,
            function () use ($query): void {
                $searchHistory = new SearchHistory();
                $searchHistory->setQuery(mb_substr($query, 0, 250));
                $searchHistory->setDatetime(new \DateTime());
                $this->em->persist($searchHistory);
                $this->em->flush();
            }
        );
    }
}
