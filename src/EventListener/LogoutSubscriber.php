<?php

namespace App\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Http\Event\LogoutEvent;
use Symfony\Bundle\SecurityBundle\Security;

class LogoutSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private RouterInterface $router,
        private Security $security,
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [LogoutEvent::class => 'onLogout'];
    }

    public function onLogout(LogoutEvent $event): void
    {
        $user = $this->security->getUser();
        $request = $event->getRequest();

        if ($user) {
            // Exemple : Message flash
            $request->getSession()->getFlashBag()->add('success', 'Vous avez été déconnecté avec succès.');
        }

        // Redirection personnalisée
        $response = new RedirectResponse(
            $this->router->generate('home.index')
        );

        $event->setResponse($response);
    }
}
