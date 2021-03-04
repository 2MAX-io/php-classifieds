<?php

declare(strict_types=1);

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class ValueExist extends Constraint
{
    public const NOT_UNIQUE_ERROR = '23bd9dbf-6b9b-41cd-a99e-4844bcf3077f';

    /** @var string[] */
    protected static $errorNames = [
        self::NOT_UNIQUE_ERROR => 'NOT_UNIQUE_ERROR',
    ];

    /** @var string */
    public $message = 'Value could not be found.';

    /** @var string */
    public $em;

    /** @var class-string */
    public $entityClass;

    /** @var string */
    public $repositoryMethod = 'findBy';

    /** @var null|string[] */
    public $fields = [];

    /** @var null|string */
    public $errorPath;

    /** @var bool */
    public $ignoreNull = true;

    public function getRequiredOptions(): array
    {
        return ['fields'];
    }

    /**
     * The validator must be defined as a service with this name.
     */
    public function validatedBy(): string
    {
        return ValueExistValidator::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }

    public function getDefaultOption(): string
    {
        return 'fields';
    }
}
