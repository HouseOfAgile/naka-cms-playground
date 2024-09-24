<?php

namespace HouseOfAgile\NakaCMSBundle\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class RedirectAfterLoginSubscriber implements EventSubscriberInterface
{
    private RequestStack $requestStack;
    private TokenStorageInterface $tokenStorage;

    public function __construct(RequestStack $requestStack, TokenStorageInterface $tokenStorage)
    {
        $this->requestStack = $requestStack;
        $this->tokenStorage = $tokenStorage;
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        $session = $this->requestStack->getSession();

        $token = $this->tokenStorage->getToken();
        $user = $token ? $token->getUser() : null;

        if ($user && $user !== 'anon.') {

            if ($session->has('redirect_to')) {
                $redirectUrl = $session->get('redirect_to');
                $session->remove('redirect_to');
                $response = new RedirectResponse($redirectUrl);
                $event->setResponse($response);
            }

        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::RESPONSE => 'onKernelResponse',
        ];
    }
}
