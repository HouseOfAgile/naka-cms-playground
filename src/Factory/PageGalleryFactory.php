<?php

namespace HouseOfAgile\NakaCMSBundle\Factory;

use HouseOfAgile\NakaCMSBundle\Entity\PageGallery;
use HouseOfAgile\NakaCMSBundle\Repository\PageGalleryRepository;
use Zenstruck\Foundry\RepositoryProxy;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @method static PageGallery|Proxy createOne(array $attributes = [])
 * @method static PageGallery[]|Proxy[] createMany(int $number, $attributes = [])
 * @method static PageGallery|Proxy findOrCreate(array $attributes)
 * @method static PageGallery|Proxy random(array $attributes = [])
 * @method static PageGallery|Proxy randomOrCreate(array $attributes = [])
 * @method static PageGallery[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static PageGallery[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static PageGalleryRepository|RepositoryProxy repository()
 * @method PageGallery|Proxy create($attributes = [])
 */
final class PageGalleryFactory extends ModelFactory
{

    public function __construct()
    {
        parent::__construct();

        // TODO inject services if required (https://github.com/zenstruck/foundry#factories-as-services)
    }

    protected function getDefaults(): array
    {
        return [
            // TODO add your default values here (https://github.com/zenstruck/foundry#model-factories)
        ];
    }

    protected function initialize(): self
    {
        // see https://github.com/zenstruck/foundry#initialization
        return $this
            // ->afterInstantiate(function(PageGallery $pageGallery) {})
        ;
    }

    protected static function getClass(): string
    {
        return PageGallery::class;
    }
}
