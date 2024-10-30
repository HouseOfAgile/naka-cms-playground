<?php

namespace HouseOfAgile\NakaCMSBundle\Security;

use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;

class AccountNotVerifiedAuthenticationException extends CustomUserMessageAccountStatusException
{
    public function getMessageKey(): string
    {
        return 'Please verify your account before logging in.';
    }
}
