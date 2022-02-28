<?php

namespace HouseOfAgile\NakaCMSBundle\Factory;

use HouseOfAgile\NakaCMSBundle\Entity\WorkshopEventTranslation;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @method static WorkshopEventTranslation|Proxy createOne(array $attributes = [])
 * @method static WorkshopEventTranslation[]|Proxy[] createMany(int $number, $attributes = [])
 * @method static WorkshopEventTranslation|Proxy findOrCreate(array $attributes)
 * @method static WorkshopEventTranslation|Proxy random(array $attributes = [])
 * @method static WorkshopEventTranslation|Proxy randomOrCreate(array $attributes = [])
 * @method static WorkshopEventTranslation[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static WorkshopEventTranslation[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method WorkshopEventTranslation|Proxy create($attributes = [])
 */
final class WorkshopEventTranslationFactory extends ModelFactory
{
    public function __construct()
    {
        parent::__construct();

        // TODO inject services if required (https://github.com/zenstruck/foundry#factories-as-services)
    }

    protected function getDefaults(): array
    {
        return [
            'mailEventText' => self::faker()->text(),
            // if locale not set we use random locale
            'locale' => rand(1, 10) > 5 ? 'fr': 'en',
        ];
    }

    protected function initialize(): self
    {
        // see https://github.com/zenstruck/foundry#initialization
        return $this
            // ->afterInstantiate(function(WorkshopEventTranslation $workshopEventTranslation) {})
        ;
    }

    protected static function getClass(): string
    {
        return WorkshopEventTranslation::class;
    }
}
