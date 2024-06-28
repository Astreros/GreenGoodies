<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\KernelEvents;

class ExceptionSubscriber implements EventSubscriberInterface
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $request = $event->getRequest();

        // TODO On génère des erreurs au format JSON seulement pour l'API
        if (str_starts_with($request->getPathInfo(), '/api/')) {
            if ($exception instanceof HttpException) {
                $data = [
                    'status' => $exception->getStatusCode(),
                    'message' => $exception->getMessage(),
                ];

            } else {
                $data = [
                    'status' => 500, // Le status n'existe pas, ce n'est pas une exception HTTP.
                    'message' => $exception->getMessage(),
                ];

            }
            $event->setResponse(new JsonResponse($data));
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }
}
