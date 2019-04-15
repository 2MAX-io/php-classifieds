<?php

declare(strict_types=1);

namespace App\Service\System\Token;

use App\Entity\Token;
use App\Entity\TokenField;

class TokenDto
{
    /**
     * @var Token
     */
    private $token;

    /**
     * @var TokenField[]
     */
    private $fields;

    public function __construct(Token $token)
    {
        $this->token = $token;
    }

    public function getFields(): array
    {
        return $this->fields;
    }

    public function setFields(array $fields): void
    {
        $this->fields = $fields;
    }

    public function addField(string $name, string $value): void
    {
        $tokenField = new TokenField();
        $tokenField->setName($name);
        $tokenField->setValue($value);

        $this->token->addField($tokenField);
    }

    public function getToken(): Token
    {
        return $this->token;
    }
}
