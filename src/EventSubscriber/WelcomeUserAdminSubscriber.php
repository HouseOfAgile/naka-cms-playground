<?php

namespace HouseOfAgile\NakaCMSBundle\EventSubscriber;

use App\Component\Communication\NotificationManager;
use App\Entity\AdminUser;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityPersistedEvent;
use HouseOfAgile\NakaCMSBundle\Entity\BaseUser;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RouterInterface;

class WelcomeUserAdminSubscriber implements EventSubscriberInterface
{
    /** @var NotificationManager */
    private $notificationManager;

    public function __construct(NotificationManager $notificationManager)
    {
        $this->notificationManager = $notificationManager;
    }

    public static function getSubscribedEvents()
    {
        return [
            AfterEntityPersistedEvent::class => ['welcomeNewEditor'],
        ];
    }

    public function welcomeNewEditor($event)
    {
        $user = $event->getEntityInstance();
        if ($user instanceof BaseUser) {
            if (in_array('ROLE_EDITOR', $user->getRoles(), true)) {
                $this->notificationManager->notificationWelcomeMessageToNewEditor($user);
            }
        }
    }
}
