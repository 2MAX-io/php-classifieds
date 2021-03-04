<?php

declare(strict_types=1);

namespace App\Service\Listing\Secondary\PoliceLog;

use App\Helper\DbHelper;
use App\Helper\SearchHelper;
use App\Service\Listing\Secondary\PoliceLog\Dto\PoliceLogForUserMessageDto;
use App\Service\Listing\Secondary\PoliceLog\Dto\PoliceLogUserMessageItemDto;
use App\Service\System\Pagination\Dto\PagerfantaAdapter;
use App\Service\System\Pagination\Dto\PaginationDto;
use App\Service\System\Pagination\PaginationService;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Statement;
use Doctrine\ORM\EntityManagerInterface;

class PoliceLogForUserMessageService
{
    /**
     * @var PaginationService
     */
    private $paginationService;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(PaginationService $paginationService, EntityManagerInterface $em)
    {
        $this->paginationService = $paginationService;
        $this->em = $em;
    }

    public function getList(PoliceLogForUserMessageDto $policeLogForUserMessageDto): PaginationDto
    {
        $where = ['true'];
        $sqlParameters = [];
        if ($policeLogForUserMessageDto->getUserId()) {
            $where[] = '( false
                || log.sender_user_id = :userId
                || log.recipient_user_id = :userId
            )';
            $sqlParameters['userId'] = $policeLogForUserMessageDto->getUserId();
        }
        if ($policeLogForUserMessageDto->getListingId()) {
            $where[] = 'log.listing_id = :listingId';
            $sqlParameters['listingId'] = $policeLogForUserMessageDto->getListingId();
        }
        if ($policeLogForUserMessageDto->getThreadId()) {
            $where[] = 'log.user_message_thread_id = :threadId';
            $sqlParameters['threadId'] = $policeLogForUserMessageDto->getThreadId();
        }
        if ($policeLogForUserMessageDto->getQuery()) {
            $where[] = '( false
                || log.sender_user_id LIKE :query 
                || log.recipient_user_id LIKE :query 
                || user_message.message LIKE :query
                || user_sender.email LIKE :query
                || user_recipient.email LIKE :query
                || listing.title LIKE :query
                || log.listing_id LIKE :query
                || log.text LIKE :query
            )';
            $sqlParameters['query'] = SearchHelper::optimizeLike($policeLogForUserMessageDto->getQuery());
        }
        $policeLogForUserMessageDto->setWhere($where);
        $policeLogForUserMessageDto->setSqlParameters($sqlParameters);

        $pagerfantaAdapter = new PagerfantaAdapter(
            $this->getResultsCount($policeLogForUserMessageDto),
            function (
                int $firstResult,
                int $maxResults
            ) use ($policeLogForUserMessageDto) {
                return $this->getResults($policeLogForUserMessageDto, $firstResult, $maxResults);
            }
        );

        $pager = $this->paginationService->createFromAdapter($pagerfantaAdapter);
        $pager->setMaxPerPage($this->paginationService->getPerPage());
        $pager->setCurrentPage($policeLogForUserMessageDto->getPage());

        return new PaginationDto($pager->getCurrentPageResults(), $pager);
    }

    /**
     * @return PoliceLogUserMessageItemDto[]
     */
    public function getResults(
        PoliceLogForUserMessageDto $policeLogForUserMessageDto,
        int $firstResult,
        int $maxResults
    ): array {
        $whereSql = \implode(' && ', $policeLogForUserMessageDto->getWhere());

        /** @var Connection|\PDO $pdo */
        $pdo = $this->em->getConnection();
        $stmt = $pdo->prepare("
            SELECT
                log.user_message_id AS userMessageId,
                log.listing_id AS listingId,
                log.user_message_thread_id AS threadId,
                log.sender_user_id AS senderUserId,
                log.recipient_user_id AS recipientUserId,
                log.datetime AS datetimeString,
                listing.slug AS listingSlug,
                listing.title AS listingTitle,
                user_message.message AS userMessage,
                log.text AS logText,
                user_sender.email AS sender,
                user_recipient.email AS recipient,
                null
            FROM zzzz_police_log_user_message AS log
            LEFT JOIN user_message ON user_message.id = log.user_message_id
            LEFT JOIN listing ON listing.id = log.listing_id
            LEFT JOIN user AS user_sender ON user_sender.id = log.sender_user_id
            LEFT JOIN user AS user_recipient ON user_recipient.id = log.recipient_user_id
            WHERE {$whereSql}
            LIMIT :firstResult, :maxResults
        ");
        $stmt->bindValue(':firstResult', $firstResult, \PDO::PARAM_INT);
        $stmt->bindValue(':maxResults', $maxResults, \PDO::PARAM_INT);
        DbHelper::bindParamsFromArray($policeLogForUserMessageDto->getSqlParameters(), $stmt);
        $stmt->setFetchMode(\PDO::FETCH_CLASS, PoliceLogUserMessageItemDto::class);
        $stmt->execute();

        return $stmt->fetchAll() ?: [];
    }

    public function getResultsCount(PoliceLogForUserMessageDto $policeLogForUserMessageDto): int
    {
        $whereSql = \implode(' && ', $policeLogForUserMessageDto->getWhere());

        $pdo = $this->em->getConnection();
        /** @var Statement|Statement[] $stmt */
        $stmt = $pdo->prepare("
            SELECT
                COUNT(1)
            FROM zzzz_police_log_user_message AS log
            LEFT JOIN user_message ON user_message.id = log.user_message_id
            LEFT JOIN listing ON listing.id = log.listing_id
            LEFT JOIN user AS user_sender ON user_sender.id = log.sender_user_id
            LEFT JOIN user AS user_recipient ON user_recipient.id = log.recipient_user_id
            WHERE {$whereSql}
        ");

        DbHelper::bindParamsFromArray($policeLogForUserMessageDto->getSqlParameters(), $stmt);
        $stmt->execute();

        return (int) $stmt->fetchOne();
    }
}
