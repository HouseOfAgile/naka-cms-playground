<?php

namespace HouseOfAgile\NakaCMSBundle\Factory;

use HouseOfAgile\NakaCMSBundle\Entity\BlockElement;
use HouseOfAgile\NakaCMSBundle\Repository\BlockElementRepository;
use Zenstruck\Foundry\RepositoryProxy;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<BlockElement>
 *
 * @method static BlockElement|Proxy createOne(array $attributes = [])
 * @method static BlockElement[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static BlockElement|Proxy find(object|array|mixed $criteria)
 * @method static BlockElement|Proxy findOrCreate(array $attributes)
 * @method static BlockElement|Proxy first(string $sortedField = 'id')
 * @method static BlockElement|Proxy last(string $sortedField = 'id')
 * @method static BlockElement|Proxy random(array $attributes = [])
 * @method static BlockElement|Proxy randomOrCreate(array $attributes = [])
 * @method static BlockElement[]|Proxy[] all()
 * @method static BlockElement[]|Proxy[] findBy(array $attributes)
 * @method static BlockElement[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static BlockElement[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static BlockElementRepository|RepositoryProxy repository()
 * @method BlockElement|Proxy create(array|callable $attributes = [])
 */
final class BlockElementFactory extends ModelFactory
{
    public function __construct()
    {
        parent::__construct();

        // TODO inject services if required (https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services)
    }

    protected function getDefaults(): array
    {
        return [
            'name' => self::faker()->word(),
            // TODO add your default values here (https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories)
        ];
    }

    protected function initialize(): self
    {
        // see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
        return $this
            // ->afterInstantiate(function(BlockElement $blockElement) {})
        ;
    }

    protected static function getClass(): string
    {
        return BlockElement::class;
    }
}
