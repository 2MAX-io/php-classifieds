<?php

declare(strict_types=1);

namespace App\Service\Payment\Dto;

use Symfony\Component\HttpFoundation\Response;

class CompletePurchaseDto
{
    /**
     * @var bool
     */
    private $isSuccess = false;

    /**
     * @var null|Response
     */
    private $redirectResponse;

    public function isSuccess(): bool
    {
        return $this->isSuccess;
    }

    public function setIsSuccess(bool $isSuccess): void
    {
        $this->isSuccess = $isSuccess;
    }

    public function getRedirectResponse(): ?Response
    {
        return $this->redirectResponse;
    }

    public function getRedirectResponseNotNull(): Response
    {
        if (null === $this->redirectResponse) {
            throw new \RuntimeException('redirectResponse is null');
        }

        return $this->redirectResponse;
    }

    public function setRedirectResponse(?Response $redirectResponse): void
    {
        $this->redirectResponse = $redirectResponse;
    }
}
