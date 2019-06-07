<?php

declare(strict_types=1);

namespace App\Service\System\Token;

use App\Entity\Token;
use App\Entity\TokenField;
use App\Entity\User;
use App\Helper\Random;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Validator\Constraints\DateTime;

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

    public function __construct(EntityManagerInterface $em, LoggerInterface $logger)
    {
        $this->em = $em;
        $this->logger = $logger;
    }

    public function getTokenBuilder(string $tokenType, ?\DateTimeInterface $validUntil = null): TokenDto
    {
        $token = new Token();
        $token->setType($tokenType);
        $token->setTokenString(Random::string(40));
        $token->setCreatedDate(new \DateTime());
        $token->setValidUntilDate($validUntil);

        return new TokenDto($token);
    }

    public function getToken(string $tokenString, string $tokenType): ?Token
    {
        $tokenEntity = $this->em->getRepository(Token::class)->findByToken($tokenString);

        if ($tokenEntity === null) {
            return null;
        }

        if ($tokenEntity->getValidUntilDate() && $tokenEntity->getValidUntilDate() < new DateTime()) {
            $this->logger->debug('token expired');

            return null;
        }

        if ($tokenEntity->getType() !== $tokenType) {
            $this->logger->critical('token found, but incorrect type', [
                $tokenString, $tokenType
            ]);

            return null;
        }

        return $tokenEntity;
    }

    public function getUserFromToken(Token $tokenEntity): User
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
