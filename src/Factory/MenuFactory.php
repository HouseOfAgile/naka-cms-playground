<?php

namespace HouseOfAgile\NakaCMSBundle\Factory;

use HouseOfAgile\NakaCMSBundle\Entity\Menu;
use HouseOfAgile\NakaCMSBundle\Repository\MenuRepository;
use Zenstruck\Foundry\RepositoryProxy;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<Menu>
 *
 * @method static Menu|Proxy createOne(array $attributes = [])
 * @method static Menu[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Menu|Proxy find(object|array|mixed $criteria)
 * @method static Menu|Proxy findOrCreate(array $attributes)
 * @method static Menu|Proxy first(string $sortedField = 'id')
 * @method static Menu|Proxy last(string $sortedField = 'id')
 * @method static Menu|Proxy random(array $attributes = [])
 * @method static Menu|Proxy randomOrCreate(array $attributes = [])
 * @method static Menu[]|Proxy[] all()
 * @method static Menu[]|Proxy[] findBy(array $attributes)
 * @method static Menu[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static Menu[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static MenuRepository|RepositoryProxy repository()
 * @method Menu|Proxy create(array|callable $attributes = [])
 */
final class MenuFactory extends ModelFactory
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
            // ->afterInstantiate(function(Menu $menu) {})
        ;
    }

    protected static function getClass(): string
    {
        return Menu::class;
    }
}
