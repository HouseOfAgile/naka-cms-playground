<?php

// src/EventSubscriber/UserLocaleSubscriber.php
namespace HouseOfAgile\NakaCMSBundle\EventSubscriber;

use App\Entity\AdminUser;
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
    private string $redirectUrl;

    public function __construct(
        private RequestStack $requestStack,
        private RouterInterface $router,
        string $redirectUrl
    ) {
        $this->redirectUrl = $redirectUrl;
    }

    public function onInteractiveLogin(InteractiveLoginEvent $event): void
    {
        $user = $event->getAuthenticationToken()->getUser();

        if (method_exists($user, 'getPreferredLocale')) {
            if (null !== $user->getPreferredLocale()) {
                $this->requestStack->getSession()->set('_locale', $user->getPreferredLocale());

				if ($user instanceof AdminUser) {
					$redirectToUrl = $this->router->generate('admin_dashboard', [
						'_locale' => $user->getPreferredLocale(),
					]);
				} else {
					$redirectToUrl = $this->router->generate($this->redirectUrl, [
						'_locale' => $user->getPreferredLocale(),
					]);
				}

                $this->requestStack->getSession()->set('redirect_to', $redirectToUrl);
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

