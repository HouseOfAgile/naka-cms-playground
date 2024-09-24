<?php
namespace HouseOfAgile\NakaCMSBundle\EventSubscriber;

use App\Entity\AdminUser;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;

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
        /** @var BaseUser $user */
        $user = $event->getAuthenticationToken()->getUser();

        if (method_exists($user, 'getPreferredLocale') && null !== $user->getPreferredLocale()) {
            $this->requestStack->getSession()->set('_locale', $user->getPreferredLocale());

            $session = $this->requestStack->getSession();
            $preferredLocale = $user->getPreferredLocale();

            if (!$session->has('redirect_to')) {
                if ($user instanceof AdminUser) {
                    $redirectToUrl = $this->router->generate('admin_dashboard', [
                        '_locale' => $preferredLocale,
                    ]);
                } else {
                    $redirectToUrl = $this->router->generate($this->redirectUrl, [
                        '_locale' => $preferredLocale,
                    ]);
                }
                $session->set('redirect_to', $redirectToUrl);
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
