<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Twig\Environment;

class ExceptionSubscriber implements EventSubscriberInterface
{
    public function __construct(private Environment $twig) {}

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => ['onKernelException', 200],
        ];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        // On traite uniquement les erreurs 404
        if ($exception instanceof NotFoundHttpException) {
            $response = new Response(
                $this->twig->render('error/404.html.twig', [
                    'exception' => $exception,
                ]),
                Response::HTTP_NOT_FOUND
            );

            $event->setResponse($response);
        }
    }
}
