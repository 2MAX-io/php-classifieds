<?php

declare(strict_types=1);

namespace App\EventListener\Exception;

use App\Exception\UserVisibleException;
use App\Helper\ExceptionHelper;
use App\Helper\Str;
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
     * @var TranslatorInterface
     */
    private $trans;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var Environment
     */
    private $twig;

    public function __construct(Environment $twig, TranslatorInterface $trans, LoggerInterface $logger)
    {
        $this->trans = $trans;
        $this->logger = $logger;
        $this->twig = $twig;
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        /** @var UserVisibleException $exception */
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
        $exceptionTrace .= ' | '.\date('Y-m-d H:i:s.U P');
        $exceptionTrace .= "\r\n".' | '.$this->getUserVisibleExceptionMessage($exception);
        $exceptionTrace .= "\r\n".' | REQUEST_TIME_FLOAT '.$request->server->get('REQUEST_TIME_FLOAT', '');
        $exceptionTrace .= "\r\n".' | REMOTE_ADDR '.$request->server->get('REMOTE_ADDR', '');
        $exceptionTrace .= "\r\n".' | REMOTE_PORT '.$request->server->get('REMOTE_PORT', '');
        $exceptionTrace .= "\r\n".' | HTTP_CF_CONNECTING_IP '.$request->server->get('HTTP_CF_CONNECTING_IP', '');
        $exceptionTrace .= "\r\n".' | HTTP_REFERER '.$request->server->get('HTTP_REFERER', '');
        $exceptionTrace .= "\r\n".' | REQUEST_URI '.$request->server->get('REQUEST_URI', '');
        $exceptionTrace .= "\r\n".' | SERVER_ADDR '.$request->server->get('SERVER_ADDR', '');
        $exceptionTrace .= "\r\n".' | SERVER_PORT '.$request->server->get('SERVER_PORT', '');

        return \base64_encode($exceptionTrace);
    }

    private function getUserVisibleExceptionMessage(\Throwable $exception): ?string
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

    private function isUserVisibleException(\Throwable $exception): bool
    {
        return UserVisibleException::class === \get_class($exception);
    }
}
