<?php

declare(strict_types=1);

namespace App\Service\System\Token;

use App\Entity\System\Token;
use App\Entity\System\TokenField;

class TokenDto
{
    /**
     * @var Token
     */
    private $tokenEntity;

    /**
     * @var TokenField[]
     */
    private $fields;

    public function __construct(Token $tokenEntity)
    {
        $this->tokenEntity = $tokenEntity;
    }

    public function getTokenEntity(): Token
    {
        return $this->tokenEntity;
    }

    /**
     * @return TokenField[]
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * @param TokenField[] $fields
     */
    public function setFields(array $fields): void
    {
        $this->fields = $fields;
    }

    public function addField(string $name, string $value): void
    {
        $tokenField = new TokenField();
        $tokenField->setName($name);
        $tokenField->setValue($value);

        $this->tokenEntity->addField($tokenField);
    }
}
