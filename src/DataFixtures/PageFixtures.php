<?php

namespace HouseOfAgile\NakaCMSBundle\DataFixtures;

use HouseOfAgile\NakaCMSBundle\Factory\CategoryFactory;
use HouseOfAgile\NakaCMSBundle\Factory\CategoryTranslationFactory;
use HouseOfAgile\NakaCMSBundle\Factory\PageFactory;
use HouseOfAgile\NakaCMSBundle\Factory\PageGalleryFactory;
use HouseOfAgile\NakaCMSBundle\Factory\PageTranslationFactory;
use HouseOfAgile\NakaCMSBundle\Factory\PictureFactory;
use HouseOfAgile\NakaCMSBundle\Factory\WorkshopEventFactory;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class PageFixtures extends BaseFixture implements DependentFixtureInterface
{

    protected $allLocales;
    public function __construct(
        $allLocales
    ) {
        $this->allLocales = $allLocales;
    }

    protected function loadData(ObjectManager $manager)
    {

        $pageTranslations = array_map(function ($locale) {
            return PageTranslationFactory::new(['locale' => $locale]);
        }, $this->allLocales);


        $categoryTranslations = array_map(function ($locale) {
            return CategoryTranslationFactory::new(['locale' => $locale]);
        }, $this->allLocales);

        CategoryFactory::createMany(5, function () use ($categoryTranslations) {
            return [
                'translations' => $categoryTranslations,
            ];
        });
        // CategoryFactory::createMany(50, function () use ($categoryTranslations) {
        //     return [
        //         'translations' => $categoryTranslations,
        //         'parentNode' => CategoryFactory::random(),
        //     ];
        // });
        PictureFactory::createMany(10);



        PageFactory::createMany(6, function () use ($pageTranslations) {
            return [
                'pageGallery' => PageGalleryFactory::createOne([
                    'images' => PictureFactory::randomRange(3, 5),
                ]),
                'translations' => $pageTranslations,
                'category' => CategoryFactory::randomRange(1, 3),
                // 'pageBlockElements' => PageBlockElementFactory::randomRange(0, 3),
                // MIG
                // 'workshopEvents' => WorkshopEventFactory::randomRange(1, 2),
            ];
        });

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return array(
            BlockElementFixtures::class,
            // WorkshopEventFixtures::class,
        );
    }
}
