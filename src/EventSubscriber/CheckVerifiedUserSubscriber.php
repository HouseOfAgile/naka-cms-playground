<?php

namespace HouseOfAgile\NakaCMSBundle\EventSubscriber;

use App\Entity\AdminUser;
use App\Entity\User;
use HouseOfAgile\NakaCMSBundle\Security\AccountNotVerifiedAuthenticationException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Event\CheckPassportEvent;
use Symfony\Component\Security\Http\Event\LoginFailureEvent;

class CheckVerifiedUserSubscriber implements EventSubscriberInterface
{
    private RouterInterface $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function onCheckPassport(CheckPassportEvent $event)
    {
        $passport = $event->getPassport();

        // Ensure the passport has a user
        $user = $passport->getUser();

        // If we have an AdminUser, we just exit
        if ($user instanceof AdminUser) return;

        if (!$user instanceof User) {
            throw new \Exception('Unexpected user type');
        }

        if (!$user->getIsVerified()) {
            throw new AccountNotVerifiedAuthenticationException(
                'Please verify your account before logging in.'
            );
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            CheckPassportEvent::class => ['onCheckPassport', -10],
            LoginFailureEvent::class => 'onLoginFailure',
        ];
    }

    public function onLoginFailure(LoginFailureEvent $event)
    {
        if (!$event->getException() instanceof AccountNotVerifiedAuthenticationException) {
            return;
        }

        $passport = $event->getPassport();
        if ($passport instanceof Passport) {
            $user = $passport->getUser();
            if ($user instanceof User) {
                $email = $user->getEmail();

                $response = new RedirectResponse(
                    $this->router->generate('app_verify_resend_email', ['email' => $email])
                );
                $event->setResponse($response);
                return;
            }
        }

        // Fallback if user is not available or not of the expected type
        $response = new RedirectResponse(
            $this->router->generate('app_verify_resend_email')
        );
        $event->setResponse($response);
    }
}
