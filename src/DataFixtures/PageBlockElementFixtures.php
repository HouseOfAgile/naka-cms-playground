<?php

namespace HouseOfAgile\NakaCMSBundle\DataFixtures;

use HouseOfAgile\NakaCMSBundle\DataFixtures\BaseFixture;
use HouseOfAgile\NakaCMSBundle\Factory\BlockElementFactory;
use HouseOfAgile\NakaCMSBundle\Factory\PageBlockElementFactory;
use HouseOfAgile\NakaCMSBundle\Factory\PageFactory;
use HouseOfAgile\NakaCMSBundle\Factory\StaticPageFactory;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class PageBlockElementFixtures  extends BaseFixture implements DependentFixtureInterface
{

    protected function loadData(ObjectManager $manager)
    {

        
        // add random blockElement

        PageBlockElementFactory::createMany(6, function () {
            return [
                'staticPage' => StaticPageFactory::find(['name' => 'homepage']),
                'blockElement' => BlockElementFactory::random(),
            ];
        });
        PageBlockElementFactory::createMany(6, function () {
            return [
                'page' => PageFactory::random(),
                'blockElement' => BlockElementFactory::random(),
            ];
        });

        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            BlockElementFixtures::class,
            // WorkshopEventFixtures::class,
        );
    }
}
