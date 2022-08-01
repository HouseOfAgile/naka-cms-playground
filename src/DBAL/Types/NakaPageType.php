<?php
namespace HouseOfAgile\NakaCMSBundle\DBAL\Types;

use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

final class NakaPageType extends AbstractEnumType
{
    public const PAGE_TYPE_CONTENT_ONLY = 'content_only';
    public const PAGE_TYPE_CONTENT_AND_BLOCKS = 'content_blocks';
    public const PAGE_TYPE_BLOCKS_ONLY = 'block_only';

    protected static array $choices = [
        self::PAGE_TYPE_CONTENT_ONLY => 'Page with only content',
        self::PAGE_TYPE_CONTENT_AND_BLOCKS => 'Page with content and blocks',
        self::PAGE_TYPE_BLOCKS_ONLY => 'Page with blocks only',
    ];

    public static function getGuessOptions()
    {
        return array_flip(self::$choices);
    }
}