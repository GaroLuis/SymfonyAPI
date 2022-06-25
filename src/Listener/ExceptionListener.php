<?php

declare(strict_types=1);

namespace App\Listener;

use App\Exception\DomainException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class ExceptionListener
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if (!($exception instanceof DomainException)) {
            return;
        }

        $event->setResponse(
            new JsonResponse(
                ['message' => $exception->getMessage()],
                $exception->getCode() ? (int) $exception->getCode() : 500
            )
        );
    }
}
