<?php

namespace HouseOfAgile\NakaCMSBundle\Factory;

use HouseOfAgile\NakaCMSBundle\Entity\StaticPageTranslation;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<StaticPageTranslation>
 *
 * @method static StaticPageTranslation|Proxy createOne(array $attributes = [])
 * @method static StaticPageTranslation[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static StaticPageTranslation|Proxy find(object|array|mixed $criteria)
 * @method static StaticPageTranslation|Proxy findOrCreate(array $attributes)
 * @method static StaticPageTranslation|Proxy first(string $sortedField = 'id')
 * @method static StaticPageTranslation|Proxy last(string $sortedField = 'id')
 * @method static StaticPageTranslation|Proxy random(array $attributes = [])
 * @method static StaticPageTranslation|Proxy randomOrCreate(array $attributes = [])
 * @method static StaticPageTranslation[]|Proxy[] all()
 * @method static StaticPageTranslation[]|Proxy[] findBy(array $attributes)
 * @method static StaticPageTranslation[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static StaticPageTranslation[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method StaticPageTranslation|Proxy create(array|callable $attributes = [])
 */
final class StaticPageTranslationFactory extends ModelFactory
{
    public function __construct()
    {
        parent::__construct();

        // TODO inject services if required (https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services)
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
        // see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
        return $this
            // ->afterInstantiate(function(StaticPageTranslation $staticPageTranslation) {})
        ;
    }

    protected static function getClass(): string
    {
        return StaticPageTranslation::class;
    }
}
