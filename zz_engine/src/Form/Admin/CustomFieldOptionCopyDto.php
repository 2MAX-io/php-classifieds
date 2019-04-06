<?php

declare(strict_types=1);

namespace App\Form\Admin;

use App\Entity\CustomField;

class CustomFieldOptionCopyDto
{
    /**
     * @var CustomField|null
     */
    private $sourceCustomField;

    public function getSourceCustomField(): ?CustomField
    {
        return $this->sourceCustomField;
    }

    public function getSourceCustomFieldNotNull(): CustomField
    {
        if (null === $this->sourceCustomField) {
            throw new \RuntimeException('sourceCustomField is null');
        }

        return $this->sourceCustomField;
    }

    public function setSourceCustomField(?CustomField $sourceCustomField): void
    {
        $this->sourceCustomField = $sourceCustomField;
    }
}
