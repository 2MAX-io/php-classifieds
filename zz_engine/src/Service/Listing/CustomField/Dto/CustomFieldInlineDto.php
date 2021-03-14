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

    /** @var string|null */
    public $unit;

    /**
     * @param array<string, mixed> $customFieldArray
     */
    public static function fromArray(array $customFieldArray): self
    {
        $customFieldInline = new self();
        $customFieldInline->name = $customFieldArray['name'];
        $customFieldInline->value = $customFieldArray['value'];
        $customFieldInline->type = $customFieldArray['type'];
        $customFieldInline->unit = $customFieldArray['unit'];

        return $customFieldInline;
    }

    /**
     * @return array<string, string|null>
     */
    public function jsonSerialize(): array
    {
        return [
            'name' => $this->name,
            'value' => $this->value,
            'type' => $this->type,
            'unit' => $this->unit,
        ];
    }
}
