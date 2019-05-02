<?php

declare(strict_types=1);

namespace App\Validator\Constraints;

use App\Service\Setting\SettingsService;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberUtil;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;
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
     * @param object     $entity
     * @param Constraint $constraint
     *
     * @throws UnexpectedTypeException
     * @throws ConstraintDefinitionException
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof Phone) {
            throw new UnexpectedTypeException($constraint, __NAMESPACE__.'\Phone');
        }

        $phoneUtil = $this->phoneNumberUtil;

        try {
            $phoneNumber = $this->phoneNumberUtil->parse($value, $this->settingsService->getLanguageTwoLetters());
        } catch (NumberParseException $e) {
            $this->addViolation($value, $constraint);

            return;
        }

        if (false === $phoneUtil->isValidNumber($phoneNumber)) {
            $this->addViolation($value, $constraint);
            return;
        }
    }

    private function addViolation($value, Constraint $constraint)
    {
        $this->context->buildViolation($constraint->message)
            ->setParameter('{{ string }}', $value)
            ->addViolation();
    }
}
