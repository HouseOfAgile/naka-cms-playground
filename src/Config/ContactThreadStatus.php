<?php

namespace HouseOfAgile\NakaCMSBundle\Config;

/**
 * A native PHP 8.1 enum listing possible statuses.
 * Doctrine can store it as a string column (enumType).
 */
enum ContactThreadStatus: string
{
    case NEW = 'new';
    case ANSWERED = 'answered';
    case SPAM = 'spam';
}
