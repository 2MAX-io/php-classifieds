<?php

declare(strict_types=1);

namespace App\Exception;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class UserVisibleException extends \Exception implements HttpExceptionInterface
{
    /**
     * @var string
     */
    private $messageKey;

    /**
     * @var array<string,string>
     */
    private $messageData;

    /**
     * @param string $message #TranslationKey
     * @param array<string,string> $messageData
     */
    public function __construct(
        string $message = '',
        array $messageData = [],
        int $code = 0,
        \Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);

        $this->setUserSafeMessage($message, $messageData);
    }

    /**
     * @param string $message #TranslationKey
     * @param array<string,string> $messageData
     */
    public static function fromPrevious(
        string $message,
        \Throwable $previous = null,
        array $messageData = [],
        int $code = 0
    ): self {
        return new self($message, $messageData, $code, $previous);
    }

    /**
     * Set a message that will be shown to the user.
     *
     * @param string $messageKey #TranslationKey
     * @param string[] $messageData Data to be passed into the translator
     */
    public function setUserSafeMessage(string $messageKey, array $messageData = []): void
    {
        $this->messageKey = $messageKey;
        $this->messageData = $messageData;
    }

    public function getUserSafeMessageKey(): string
    {
        return $this->messageKey;
    }

    /**
     * @return array<string,string>
     */
    public function getUserSafeMessageData(): array
    {
        return $this->messageData;
    }

    /**
     * Returns the status code.
     *
     * @return int An HTTP response status code
     */
    public function getStatusCode(): int
    {
        return Response::HTTP_INTERNAL_SERVER_ERROR;
    }

    /**
     * Returns response headers.
     *
     * @return array<string,string> Response headers
     */
    public function getHeaders(): array
    {
        return [];
    }
}
