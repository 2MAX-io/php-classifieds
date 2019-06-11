<?php

declare(strict_types=1);

namespace App\Validator\Constraints;

use App\Service\Setting\SettingsService;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class HasLetterNumberValidator extends ConstraintValidator
{
    /**
     * @var SettingsService
     */
    private $settingsService;

    public function __construct(SettingsService $settingsService)
    {
        $this->settingsService = $settingsService;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof HasLetterNumber) {
            throw new UnexpectedTypeException($constraint, __NAMESPACE__.'\HasLetterNumber');
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!is_scalar($value) && !(\is_object($value) && method_exists($value, '__toString'))) {
            throw new UnexpectedValueException($value, 'string');
        }

        $value = (string) $value;


        $allowedCharacters = '';
        $allowedCharacters .= 'qwertyuiopasdfghjklzxcvbnm';
        $allowedCharacters .= 'QWERTYUIOPASDFGHJKLZXCVBNM';
        $allowedCharacters .= '1234567890';
        $allowedCharacters .= $this->settingsService->getAllowedCharacters();

        if (!\preg_match('~[' . \preg_quote($allowedCharacters, '~') . ']+~', $value)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $this->formatValue($value))
                ->addViolation();
        }
    }
}
