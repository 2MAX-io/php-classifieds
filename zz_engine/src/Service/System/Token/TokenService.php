<?php

declare(strict_types=1);

namespace App\Service\System\Token;

use App\Entity\System\Token;
use App\Entity\System\TokenField;
use App\Entity\User;
use App\Helper\DateHelper;
use App\Helper\RandomHelper;
use App\Repository\TokenRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class TokenService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var TokenRepository
     */
    private $tokenRepository;

    public function __construct(TokenRepository $tokenRepository, EntityManagerInterface $em, LoggerInterface $logger)
    {
        $this->tokenRepository = $tokenRepository;
        $this->em = $em;
        $this->logger = $logger;
    }

    public function createToken(string $tokenType, ?\DateTimeInterface $validUntil = null): TokenDto
    {
        $token = new Token();
        $token->setType($tokenType);
        $token->setTokenString(RandomHelper::string(40));
        $token->setCreatedDate(DateHelper::create());
        $token->setValidUntilDate($validUntil);

        return new TokenDto($token);
    }

    public function getToken(string $tokenString, string $tokenType): ?Token
    {
        $tokenEntity = $this->tokenRepository->findByToken($tokenString);
        if (null === $tokenEntity) {
            return null;
        }

        if ($tokenEntity->getValidUntilDate() && $tokenEntity->getValidUntilDate() < DateHelper::create()) {
            $this->logger->debug('token expired');

            return null;
        }

        if ($tokenEntity->getType() !== $tokenType) {
            $this->logger->critical('token found, but incorrect type', [
                $tokenString, $tokenType,
            ]);

            return null;
        }

        return $tokenEntity;
    }

    public function getUserFromToken(Token $tokenEntity): ?User
    {
        $userId = $tokenEntity->getFieldByName(TokenField::USER_ID_FIELD);

        return $this->em->getRepository(User::class)->find($userId);
    }

    public function markTokenUsed(Token $token): void
    {
        $token->setUsed(true);
        $this->em->persist($token);
    }
}
