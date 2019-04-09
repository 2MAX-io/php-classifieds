<?php

declare(strict_types=1);

namespace App\System;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class EnvironmentService
{
    /**
     * @var ParameterBagInterface
     */
    private $parameterBag;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->parameterBag = $parameterBag;
    }

    public function getMailerFromEmailAddress(): string
    {
        return $this->parameterBag->get('mailer_from_email_address');
    }
}
