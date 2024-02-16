<?php

namespace App\EventSubscriber;

use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Event\LogoutEvent;

class LogoutSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly UrlGeneratorInterface $urlGenerator)
    {

    }
    public function onLogoutEvent(LogoutEvent $event): void
    {
        // get the security token of the session that is about to be logged out
        $token = $event->getToken();

        // get the current request
        $request = $event->getRequest();

        // get the current response, if it is already set by another listener
        $response = $event->getResponse();

        // configure a custom logout response to the homepage
        $response = new RedirectResponse(
            $this->urlGenerator->generate('app_duo'),
            Response::HTTP_SEE_OTHER
        );
        $event->setResponse($response);
    }

    #[ArrayShape([LogoutEvent::class => "string"])]
    public static function getSubscribedEvents(): array
    {
        return [
            LogoutEvent::class => 'onLogoutEvent',
        ];
    }
}
