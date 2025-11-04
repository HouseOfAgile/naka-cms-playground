<?php

namespace HouseOfAgile\NakaCMSBundle\Event;

use HouseOfAgile\NakaCMSBundle\Entity\BaseUser;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Event dispatched when a new user registers on the platform
 * This event is used to trigger notifications and post-registration workflows
 */
class UserRegistrationEvent extends Event
{
    public const NAME = 'naka_cms.user.registration';

    private BaseUser $user;
    private array $context;

    public function __construct(BaseUser $user, array $context = [])
    {
        $this->user = $user;
        $this->context = $context;
    }

    public function getUser(): BaseUser
    {
        return $this->user;
    }

    public function getContext(): array
    {
        return $this->context;
    }

    public function getContextValue(string $key, mixed $default = null): mixed
    {
        return $this->context[$key] ?? $default;
    }
}
