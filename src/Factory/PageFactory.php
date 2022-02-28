<?php

namespace HouseOfAgile\NakaCMSBundle\Factory;

use HouseOfAgile\NakaCMSBundle\Entity\Page;
use HouseOfAgile\NakaCMSBundle\Repository\PageRepository;
use Zenstruck\Foundry\RepositoryProxy;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @method static Page|Proxy createOne(array $attributes = [])
 * @method static Page[]|Proxy[] createMany(int $number, $attributes = [])
 * @method static Page|Proxy findOrCreate(array $attributes)
 * @method static Page|Proxy random(array $attributes = [])
 * @method static Page|Proxy randomOrCreate(array $attributes = [])
 * @method static Page[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static Page[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static PageRepository|RepositoryProxy repository()
 * @method Page|Proxy create($attributes = [])
 */
final class PageFactory extends ModelFactory
{
    public function __construct()
    {
        parent::__construct();

        // TODO inject services if required (https://github.com/zenstruck/foundry#factories-as-services)
    }

    protected function getDefaults(): array
    {
        return [
            // 'name' => 'Missing pants',
            // 'slug' => 'missing-pants-'.rand(0, 1000),
            // TODO add your default values here (https://github.com/zenstruck/foundry#model-factories)
        ];
    }

    protected function initialize(): self
    {
        // see https://github.com/zenstruck/foundry#initialization
        return $this
            // ->afterInstantiate(function(Page $page) {})
        ;
    }

    protected static function getClass(): string
    {
        return Page::class;
    }
}
