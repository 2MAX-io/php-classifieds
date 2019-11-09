<?php
declare(strict_types=1);

namespace App\Service\Payment;

use Symfony\Component\HttpFoundation\Response;

class CompletePurchaseDto
{
    /**
     * @var bool
     */
    private $isRedirect = false;

    /**
     * @var null|Response
     */
    private $response;

    public function isRedirect(): bool
    {
        return $this->isRedirect;
    }

    public function setIsRedirect(bool $isRedirect): void
    {
        $this->isRedirect = $isRedirect;
    }

    public function getResponse(): ?Response
    {
        return $this->response;
    }

    public function setResponse(?Response $response): void
    {
        $this->response = $response;
    }
}