<?php

namespace HouseOfAgile\NakaCMSBundle\Factory;

use HouseOfAgile\NakaCMSBundle\Entity\MenuItem;
use HouseOfAgile\NakaCMSBundle\Repository\MenuItemRepository;
use Zenstruck\Foundry\RepositoryProxy;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<MenuItem>
 *
 * @method static MenuItem|Proxy createOne(array $attributes = [])
 * @method static MenuItem[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static MenuItem|Proxy find(object|array|mixed $criteria)
 * @method static MenuItem|Proxy findOrCreate(array $attributes)
 * @method static MenuItem|Proxy first(string $sortedField = 'id')
 * @method static MenuItem|Proxy last(string $sortedField = 'id')
 * @method static MenuItem|Proxy random(array $attributes = [])
 * @method static MenuItem|Proxy randomOrCreate(array $attributes = [])
 * @method static MenuItem[]|Proxy[] all()
 * @method static MenuItem[]|Proxy[] findBy(array $attributes)
 * @method static MenuItem[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static MenuItem[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static MenuItemRepository|RepositoryProxy repository()
 * @method MenuItem|Proxy create(array|callable $attributes = [])
 */
final class MenuItemFactory extends ModelFactory
{
    public function __construct()
    {
        parent::__construct();

        // TODO inject services if required (https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services)
    }

    protected function getDefaults(): array
    {
        return [
            // TODO add your default values here (https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories)
            'name' => self::faker()->text(),
        ];
    }

    protected function initialize(): self
    {
        // see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
        return $this
            // ->afterInstantiate(function(MenuItem $menuItem) {})
        ;
    }

    protected static function getClass(): string
    {
        return MenuItem::class;
    }
}
