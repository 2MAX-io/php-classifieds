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

    public function getTwigDateFormat(): string
    {
        return $this->parameterBag->get('twig_date_format');
    }

    public function getTwigDateFormatShort(): string
    {
        return $this->parameterBag->get('twig_date_format_short');
    }

    public function getAppTimezone(): string
    {
        return $this->parameterBag->get('timezone');
    }
}
