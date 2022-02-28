<?php

namespace HouseOfAgile\NakaCMSBundle\Factory;

use HouseOfAgile\NakaCMSBundle\Entity\CategoryTranslation;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @method static CategoryTranslation|Proxy createOne(array $attributes = [])
 * @method static CategoryTranslation[]|Proxy[] createMany(int $number, $attributes = [])
 * @method static CategoryTranslation|Proxy findOrCreate(array $attributes)
 * @method static CategoryTranslation|Proxy random(array $attributes = [])
 * @method static CategoryTranslation|Proxy randomOrCreate(array $attributes = [])
 * @method static CategoryTranslation[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static CategoryTranslation[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method CategoryTranslation|Proxy create($attributes = [])
 */
final class CategoryTranslationFactory extends ModelFactory
{
    public function __construct()
    {
        parent::__construct();

        // TODO inject services if required (https://github.com/zenstruck/foundry#factories-as-services)
    }

    protected function getDefaults(): array
    {
        return [
            'name' => self::faker()->word(),
            'locale' => rand(1, 10) > 5 ? 'fr': 'en',        ];
    }

    protected function initialize(): self
    {
        // see https://github.com/zenstruck/foundry#initialization
        return $this
            // ->afterInstantiate(function(CategoryTranslation $categoryTranslation) {})
        ;
    }

    protected static function getClass(): string
    {
        return CategoryTranslation::class;
    }
}
