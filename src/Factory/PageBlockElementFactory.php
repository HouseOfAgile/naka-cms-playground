<?php

namespace HouseOfAgile\NakaCMSBundle\Factory;

use HouseOfAgile\NakaCMSBundle\Entity\PageBlockElement;
use HouseOfAgile\NakaCMSBundle\Repository\PageBlockElementRepository;
use Zenstruck\Foundry\RepositoryProxy;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<PageBlockElement>
 *
 * @method static PageBlockElement|Proxy createOne(array $attributes = [])
 * @method static PageBlockElement[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static PageBlockElement|Proxy find(object|array|mixed $criteria)
 * @method static PageBlockElement|Proxy findOrCreate(array $attributes)
 * @method static PageBlockElement|Proxy first(string $sortedField = 'id')
 * @method static PageBlockElement|Proxy last(string $sortedField = 'id')
 * @method static PageBlockElement|Proxy random(array $attributes = [])
 * @method static PageBlockElement|Proxy randomOrCreate(array $attributes = [])
 * @method static PageBlockElement[]|Proxy[] all()
 * @method static PageBlockElement[]|Proxy[] findBy(array $attributes)
 * @method static PageBlockElement[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static PageBlockElement[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static PageBlockElementRepository|RepositoryProxy repository()
 * @method PageBlockElement|Proxy create(array|callable $attributes = [])
 */
final class PageBlockElementFactory extends ModelFactory
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
            'position' => self::faker()->randomNumber(),
        ];
    }

    protected function initialize(): self
    {
        // see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
        return $this
            // ->afterInstantiate(function(PageBlockElement $pageBlockElement) {})
        ;
    }

    protected static function getClass(): string
    {
        return PageBlockElement::class;
    }
}
