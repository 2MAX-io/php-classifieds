<?php

declare(strict_types=1);

namespace App\Validator\Constraints;

use App\Service\Setting\SettingsService;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberUtil;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class PhoneValidator extends ConstraintValidator
{
    /**
     * @var PhoneNumberUtil
     */
    private $phoneNumberUtil;

    /**
     * @var SettingsService
     */
    private $settingsService;

    public function __construct(PhoneNumberUtil $phoneNumberUtil, SettingsService $settingsService)
    {
        $this->phoneNumberUtil = $phoneNumberUtil;
        $this->settingsService = $settingsService;
    }

    /**
     * @inheritDoc
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof Phone) {
            throw new UnexpectedTypeException($constraint, Phone::class);
        }

        if (empty($value)) {
            return;
        }

        $phoneUtil = $this->phoneNumberUtil;
        try {
            $phoneNumber = $phoneUtil->parse($value, $this->settingsService->getLanguageTwoLetters());
        } catch (NumberParseException $e) {
            $this->addViolation($value, $constraint);

            return;
        }

        if (false === $phoneUtil->isValidNumber($phoneNumber)) {
            $this->addViolation($value, $constraint);

            return;
        }
    }

    private function addViolation(string $value, Phone $constraint): void
    {
        $this->context->buildViolation($constraint->message)
            ->setParameter('{{ string }}', $value)
            ->addViolation();
    }
}
