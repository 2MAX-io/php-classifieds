<?php

declare(strict_types=1);

namespace App\Service\Listing\CustomField\Dto;

class CustomFieldInlineDto implements \JsonSerializable
{
    /** @var string */
    public $name;

    /** @var string */
    public $value;

    /** @var string */
    public $type;

    /**
     * @return array<string, string>
     */
    public function jsonSerialize(): array
    {
        return [
            'name' => $this->name,
            'value' => $this->value,
            'type' => $this->type,
        ];
    }
}
