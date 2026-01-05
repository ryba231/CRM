<?php

namespace App\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

final class ApiExceptionListener
{
    #[AsEventListener]
    public function onExceptionEvent(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if($exception instanceof \DomainException)
        {
            $event->setResponse(
                new JsonResponse(
                    ['error' => $exception->getMessage()],
                    409
                )
            );
            return;
        }

        if ($exception instanceof HttpExceptionInterface)
        {
            $event->setResponse(
                new JsonResponse(
                    ['error' => $exception->getMessage()],
                    $exception->getStatusCode()
                )
            );
            return;
        }
    }
}
