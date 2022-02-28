<?php

namespace HouseOfAgile\NakaCMSBundle\Factory;

use HouseOfAgile\NakaCMSBundle\Entity\WorkshopEvent;
use HouseOfAgile\NakaCMSBundle\Repository\WorkshopEventRepository;
use Zenstruck\Foundry\RepositoryProxy;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @method static WorkshopEvent|Proxy createOne(array $attributes = [])
 * @method static WorkshopEvent[]|Proxy[] createMany(int $number, $attributes = [])
 * @method static WorkshopEvent|Proxy findOrCreate(array $attributes)
 * @method static WorkshopEvent|Proxy random(array $attributes = [])
 * @method static WorkshopEvent|Proxy randomOrCreate(array $attributes = [])
 * @method static WorkshopEvent[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static WorkshopEvent[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static WorkshopEventRepository|RepositoryProxy repository()
 * @method WorkshopEvent|Proxy create($attributes = [])
 */
final class WorkshopEventFactory extends ModelFactory
{
    public function __construct()
    {
        parent::__construct();

        // TODO inject services if required (https://github.com/zenstruck/foundry#factories-as-services)
    }

    protected function getDefaults(): array
    {
        $startDate = self::faker()->dateTimeBetween('2 weeks', '+2 weeks');
        $endDate = self::faker()->dateTimeBetween('4 weeks', '+4 weeks');
        return [
            'startDate' => $startDate->modify(sprintf('+%s hours', rand(1, 9))),
            'endDate' => $endDate->modify(sprintf('+%s hours', rand(1, 9))),
            // TODO add your default values here (https://github.com/zenstruck/foundry#model-factories)
        ];
    }

    protected function initialize(): self
    {
        // see https://github.com/zenstruck/foundry#initialization
        return $this
            // ->afterInstantiate(function(WorkshopEvent $workshopEvent) {})
        ;
    }

    protected static function getClass(): string
    {
        return WorkshopEvent::class;
    }
}
