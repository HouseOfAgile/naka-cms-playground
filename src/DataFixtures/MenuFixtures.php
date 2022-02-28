<?php

namespace HouseOfAgile\NakaCMSBundle\DataFixtures;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use HouseOfAgile\NakaCMSBundle\DataFixtures\PageFixtures;
use HouseOfAgile\NakaCMSBundle\DBAL\Types\NakaMenuItemType;
use HouseOfAgile\NakaCMSBundle\Entity\Page;
use HouseOfAgile\NakaCMSBundle\Factory\MenuFactory;
use HouseOfAgile\NakaCMSBundle\Factory\MenuItemFactory;

class MenuFixtures extends BaseFixture implements DependentFixtureInterface
{

    protected $allLocales;
    public function __construct(
        $allLocales
    ) {
        $this->allLocales = $allLocales;
    }

    protected function loadData(ObjectManager $manager)
    {
        $allPages = $manager->getRepository(Page::class)->findAll();


        foreach ($allPages as $key => $page) {
            MenuItemFactory::createOne(
                [
                    'name' => sprintf('Menu Route %s', $page->getId()),
                    'type' => NakaMenuItemType::TYPE_ROUTE,
                    'route' => 'page_view',
                    'routeParameters' => ['page' => $page],
                ]
            );

            # code...
        }

        MenuFactory::createOne(
            [
                'name' => 'mainMenu',
                'menuItems' => MenuItemFactory::randomRange(3, 5),
            ]
        );
        MenuFactory::createMany(3, function () {
            return [
                'menuItems' => MenuItemFactory::randomRange(3, 5),
            ];
        });

        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            PageFixtures::class,
        );
    }
}
