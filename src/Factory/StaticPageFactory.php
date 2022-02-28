<?php

namespace HouseOfAgile\NakaCMSBundle\Factory;

use HouseOfAgile\NakaCMSBundle\Entity\StaticPage;
use HouseOfAgile\NakaCMSBundle\Repository\StaticPageRepository;
use Zenstruck\Foundry\RepositoryProxy;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<StaticPage>
 *
 * @method static StaticPage|Proxy createOne(array $attributes = [])
 * @method static StaticPage[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static StaticPage|Proxy find(object|array|mixed $criteria)
 * @method static StaticPage|Proxy findOrCreate(array $attributes)
 * @method static StaticPage|Proxy first(string $sortedField = 'id')
 * @method static StaticPage|Proxy last(string $sortedField = 'id')
 * @method static StaticPage|Proxy random(array $attributes = [])
 * @method static StaticPage|Proxy randomOrCreate(array $attributes = [])
 * @method static StaticPage[]|Proxy[] all()
 * @method static StaticPage[]|Proxy[] findBy(array $attributes)
 * @method static StaticPage[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static StaticPage[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static StaticPageRepository|RepositoryProxy repository()
 * @method StaticPage|Proxy create(array|callable $attributes = [])
 */
final class StaticPageFactory extends ModelFactory
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
            // ->afterInstantiate(function(StaticPage $staticPage) {})
        ;
    }

    protected static function getClass(): string
    {
        return StaticPage::class;
    }
}
