<?php

declare(strict_types=1);

namespace App\Service\Listing\Secondary;

use App\Entity\Log\SearchHistory;
use App\Helper\DateHelper;
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

    public function saveSearch(string $query, int $resultsCount): void
    {
        $query = \trim($query);
        if (empty($query) || \strlen($query) < 2) {
            return;
        }

        $this->eventDispatcher->addListener(
            KernelEvents::TERMINATE,
            function () use ($query, $resultsCount): void {
                $searchHistory = new SearchHistory();
                $searchHistory->setQuery(\mb_substr($query, 0, 250));
                $searchHistory->setResultsCount($resultsCount);
                $searchHistory->setDatetime(DateHelper::create());
                $this->em->persist($searchHistory);
                $this->em->flush();
            }
        );
    }
}
