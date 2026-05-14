<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Bundle\SecurityBundle\Security;
use Twig\Environment;

class ExceptionSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private Environment $twig,
        private Security $security
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => ['onKernelException', 200],
        ];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if ($exception instanceof NotFoundHttpException) {

            $user = $event->getRequest()->getSession()->get('_security_main')
                ? unserialize($event->getRequest()->getSession()->get('_security_main'))?->getUser()
                : null;
            $showDisconnect = null;
            if( $user !== null ) $showDisconnect = true;

            //dd($showDisconnect, $user);

            $html = $this->twig->render('error/404.html.twig', [
                'exception' => $exception,
                'show_disconnect'      => $showDisconnect,           // On passe une variable pour détecter quel affichage fournir
            ]);

            $response = new Response($html, Response::HTTP_NOT_FOUND);
            $event->setResponse($response);
        }
    }
}
