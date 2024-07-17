<?php

// src/EventSubscriber/UserLocaleSubscriber.php
namespace HouseOfAgile\NakaCMSBundle\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;

/**
 * Stores the locale of the user in the session after the
 * login. This can be used by the LocaleSubscriber afterwards.
 */
class UserLocaleSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private RequestStack $requestStack,
		private RouterInterface $router,
		) {
    }

    public function onInteractiveLogin(InteractiveLoginEvent $event): void
    {
        $user = $event->getAuthenticationToken()->getUser();

        if (method_exists($user, 'getPreferredLocale')) {
            // User entity might not have the locale attribute
            if (null !== $user->getPreferredLocale()) {
                // dd($this->requestStack->getMainRequest()->attributes);
                $this->requestStack->getSession()->set('_locale', $user->getPreferredLocale());


                // Generate the URL with the correct locale
                $linkToAccount = $this->router->generate('account_bookings', [
                    '_locale' => $user->getPreferredLocale(),
                ]);
                $this->requestStack->getSession()->set('redirect_to', $linkToAccount);

            }
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            SecurityEvents::INTERACTIVE_LOGIN => 'onInteractiveLogin',
        ];
    }
}
