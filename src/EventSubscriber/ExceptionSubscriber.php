<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\KernelEvents;

class ExceptionSubscriber implements EventSubscriberInterface
{
    /**
     * Gère les exceptions pour les routes API.
     *
     * Cette méthode est déclenchée chaque fois qu'une exception est lancée dans l'application.
     * Elle vérifie si le chemin de la requête commence par '/api/' et formate l'exception
     * en une réponse JSON avec le code de statut et le message appropriés.
     *
     * @param ExceptionEvent $event L'objet événement contenant les détails de l'exception.
     *
     * @return void
     */
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $request = $event->getRequest();

        if (str_starts_with($request->getPathInfo(), '/api/')) {
            if ($exception instanceof HttpException) {
                $data = [
                    'status' => $exception->getStatusCode(),
                    'message' => $exception->getMessage(),
                ];

            } else {
                $data = [
                    'status' => 500,
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
