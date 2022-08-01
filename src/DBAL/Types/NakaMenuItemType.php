<?php
namespace HouseOfAgile\NakaCMSBundle\DBAL\Types;

use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

final class NakaMenuItemType extends AbstractEnumType
{
    public const TYPE_ROUTE = 'route';
    public const TYPE_URL = 'url';
    public const TYPE_PAGE = 'page';

    protected static array $choices = [
        self::TYPE_ROUTE => 'Menu Item of type route',
        self::TYPE_URL => 'Menu Item of type url',
        self::TYPE_PAGE => 'Menu Item of type page',
    ];

    public static function getGuessOptions()
    {
        return array_flip(self::$choices);
    }
}