<?php

namespace HouseOfAgile\NakaCMSBundle\Factory;

use HouseOfAgile\NakaCMSBundle\Entity\PageTranslation;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @method static PageTranslation|Proxy createOne(array $attributes = [])
 * @method static PageTranslation[]|Proxy[] createMany(int $number, $attributes = [])
 * @method static PageTranslation|Proxy findOrCreate(array $attributes)
 * @method static PageTranslation|Proxy random(array $attributes = [])
 * @method static PageTranslation|Proxy randomOrCreate(array $attributes = [])
 * @method static PageTranslation[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static PageTranslation[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method PageTranslation|Proxy create($attributes = [])
 */
final class PageTranslationFactory extends ModelFactory
{
    public function __construct()
    {
        parent::__construct();

        // TODO inject services if required (https://github.com/zenstruck/foundry#factories-as-services)
    }

    protected function getDefaults(): array
    {
        return [
            'title' => self::faker()->sentence(),
            'content' => self::faker()->text(rand(500,5000)),
            // if locale not set we use random locale
            'locale' => rand(1, 10) > 5 ? 'fr': 'en',
        ];
    }

    protected function initialize(): self
    {
        // see https://github.com/zenstruck/foundry#initialization
        return $this
            // ->afterInstantiate(function(PageTranslation $pageTranslation) {})
        ;
    }

    protected static function getClass(): string
    {
        return PageTranslation::class;
    }
}
