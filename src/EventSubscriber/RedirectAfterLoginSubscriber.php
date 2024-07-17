<?php 

// src/EventSubscriber/RedirectAfterLoginSubscriber.php
namespace HouseOfAgile\NakaCMSBundle\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class RedirectAfterLoginSubscriber implements EventSubscriberInterface
{
    public function __construct(private RequestStack $requestStack)
    {
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        $session = $this->requestStack->getSession();
        if ($session->has('redirect_to')) {
            $redirectUrl = $session->get('redirect_to');
            $session->remove('redirect_to');

            $response = new RedirectResponse($redirectUrl);
            $event->setResponse($response);
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::RESPONSE => 'onKernelResponse',
        ];
    }
}
