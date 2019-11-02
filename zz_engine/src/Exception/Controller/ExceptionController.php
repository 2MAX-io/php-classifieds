<?php

declare(strict_types=1);

namespace App\Exception\Controller;

use App\Exception\UserVisibleException;
use App\Exception\UserVisibleMessageException;
use App\Helper\ExceptionHelper;
use App\Helper\Str;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\TwigBundle\Controller\ExceptionController as BaseExceptionController;
use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class ExceptionController extends BaseExceptionController
{
    /**
     * @var TranslatorInterface
     */
    private $trans;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param Environment $twig
     * @param bool        $debug Show error (false) or exception (true) pages by default
     */
    public function __construct(Environment $twig, LoggerInterface $logger, TranslatorInterface $trans, bool $debug)
    {
        parent::__construct($twig, $debug);
        $this->twig = $twig;
        $this->debug = $debug;
        $this->trans = $trans;
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     */
    public function showAction(Request $request, FlattenException $exception, DebugLoggerInterface $logger = null): Response
    {
        if ($this->isUserVisibleException($exception)) {
            $this->debug = false;
        }

        $currentContent = $this->getAndCleanOutputBuffering($request->headers->get('X-Php-Ob-Level', -1));
        $showException = $request->attributes->get('showException', $this->debug); // As opposed to an additional parameter, this maintains BC

        $code = $exception->getStatusCode();

        return new Response($this->twig->render(
            (string) $this->findTemplate($request, $request->getRequestFormat(), $code, $showException),
            [
                'status_code' => $code,
                'status_text' => Response::$statusTexts[$code] ?? '',
                'exception' => $exception,
                'logger' => $logger,
                'currentContent' => $currentContent,
                'userVisibleExceptionMessage' => $this->getUserVisibleExceptionMessage($exception),
                'exceptionTrace' => $this->getExceptionTrace($request, $exception),
            ]
        ), 200, ['Content-Type' => $request->getMimeType($request->getRequestFormat()) ?: 'text/html']);
    }

    private function getExceptionTrace(Request $request, FlattenException $exception): string
    {
        $exceptionTrace = '';
        $exceptionTrace .= ' | ' . \date('Y-m-d H:i:s.U P');
        $exceptionTrace .= "\r\n" . ' | ' . $this->getUserVisibleExceptionMessage($exception);
        $exceptionTrace .= "\r\n" . ' | REQUEST_TIME_FLOAT ' . $request->server->get('REQUEST_TIME_FLOAT', '');
        $exceptionTrace .= "\r\n" . ' | REMOTE_ADDR ' . $request->server->get('REMOTE_ADDR', '');
        $exceptionTrace .= "\r\n" . ' | REMOTE_PORT ' . $request->server->get('REMOTE_PORT', '');
        $exceptionTrace .= "\r\n" . ' | HTTP_CF_CONNECTING_IP ' . $request->server->get('HTTP_CF_CONNECTING_IP', '');
        $exceptionTrace .= "\r\n" . ' | HTTP_REFERER ' . $request->server->get('HTTP_REFERER', '');
        $exceptionTrace .= "\r\n" . ' | REQUEST_URI ' . $request->server->get('REQUEST_URI', '');
        $exceptionTrace .= "\r\n" . ' | SERVER_ADDR ' . $request->server->get('SERVER_ADDR', '');
        $exceptionTrace .= "\r\n" . ' | SERVER_PORT ' . $request->server->get('SERVER_PORT', '');

        return \base64_encode($exceptionTrace);
    }

    private function getUserVisibleExceptionMessage(FlattenException $exception): ?string
    {
        if (!$this->isUserVisibleException($exception)) {
            return null;
        }

        $message = $exception->getMessage();
        try {
            if (Str::beginsWith($message, 'trans.')) {
                return $this->trans->trans($message);
            }
        } catch (\Throwable $e) {
            $this->logger->error($e->getMessage(), ExceptionHelper::flatten($e));
        }

        return $message;
    }

    private function isUserVisibleException(FlattenException $exception): bool
    {
        return UserVisibleException::class === $exception->getClass();
    }
}
