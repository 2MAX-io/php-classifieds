<?php

declare(strict_types=1);

namespace App\Service\Listing\CustomField\Dto;

class CustomFieldFromRequestDto
{
    /**
     * @var int
     */
    private $customFieldId;

    /**
     * @var string
     */
    private $customFieldValueString;

    public function getCustomFieldId(): int
    {
        return $this->customFieldId;
    }

    public function setCustomFieldId(int $customFieldId): void
    {
        $this->customFieldId = $customFieldId;
    }

    public function getCustomFieldValueString(): string
    {
        return $this->customFieldValueString;
    }

    public function setCustomFieldValueString(string $customFieldValueString): void
    {
        $this->customFieldValueString = $customFieldValueString;
    }
}
