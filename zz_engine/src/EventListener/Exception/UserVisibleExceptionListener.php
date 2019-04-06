<?php

declare(strict_types=1);

namespace App\EventListener\Exception;

use App\Exception\UserVisibleException;
use App\Helper\DateHelper;
use App\Helper\ExceptionHelper;
use App\Helper\StringHelper;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class UserVisibleExceptionListener
{
    /**
     * @var Environment
     */
    private $twig;

    /**
     * @var TranslatorInterface
     */
    private $trans;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(Environment $twig, TranslatorInterface $trans, LoggerInterface $logger)
    {
        $this->twig = $twig;
        $this->trans = $trans;
        $this->logger = $logger;
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        /** @var \Throwable|UserVisibleException $exception */
        $exception = $event->getThrowable();
        $request = $event->getRequest();
        if (!$this->isUserVisibleException($exception)) {
            return;
        }

        $response = new Response();
        $response->setContent($this->twig->render('system/exception/user_visible_exception.html.twig', [
            'userVisibleExceptionMessage' => $this->getUserVisibleExceptionMessage($exception),
            'exceptionReferenceMessage' => $this->getExceptionTrace($request, $exception),
        ]));

        // HttpExceptionInterface is a special type of exception that
        // holds status code and header details
        if ($exception instanceof HttpExceptionInterface) {
            $response->setStatusCode($exception->getStatusCode());
            $response->headers->replace($exception->getHeaders());
        } else {
            $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        // sends the modified response object to the event
        $event->setResponse($response);
    }

    private function getExceptionTrace(Request $request, \Throwable $exception): string
    {
        $exceptionTrace = '';
        $exceptionTrace .= 'Error: '.$this->getUserVisibleExceptionMessage($exception)."\r\n";
        $exceptionTrace .= 'Date: '.DateHelper::date('Y-m-d H:i:s.U P');
        $exceptionTrace .= 'REQUEST_TIME_FLOAT '.$request->server->get('REQUEST_TIME_FLOAT', '')."\r\n";
        $exceptionTrace .= 'REMOTE_ADDR '.$request->server->get('REMOTE_ADDR', '')."\r\n";
        $exceptionTrace .= 'REMOTE_PORT '.$request->server->get('REMOTE_PORT', '')."\r\n";
        $exceptionTrace .= 'HTTP_CF_CONNECTING_IP '.$request->server->get('HTTP_CF_CONNECTING_IP', '')."\r\n";
        $exceptionTrace .= 'HTTP_REFERER '.$request->server->get('HTTP_REFERER', '')."\r\n";
        $exceptionTrace .= 'REQUEST_URI '.$request->server->get('REQUEST_URI', '')."\r\n";
        $exceptionTrace .= 'SERVER_ADDR '.$request->server->get('SERVER_ADDR', '')."\r\n";
        $exceptionTrace .= 'SERVER_PORT '.$request->server->get('SERVER_PORT', '')."\r\n";

        return $exceptionTrace;
    }

    private function getUserVisibleExceptionMessage(\Throwable $exception): ?string
    {
        if (!$this->isUserVisibleException($exception)) {
            return null;
        }

        $message = $exception->getMessage();

        try {
            if (StringHelper::beginsWith($message, 'trans.')) {
                return $this->trans->trans($message);
            }
        } catch (\Throwable $e) {
            $this->logger->error($e->getMessage(), ExceptionHelper::flatten($e));
        }

        return $message;
    }

    private function isUserVisibleException(\Throwable $exception): bool
    {
        return UserVisibleException::class === \get_class($exception);
    }
}
