<?php

namespace HouseOfAgile\NakaCMSBundle\DataFixtures;

use HouseOfAgile\NakaCMSBundle\DataFixtures\BaseFixture;
use HouseOfAgile\NakaCMSBundle\Factory\BlockElementFactory;
use HouseOfAgile\NakaCMSBundle\Factory\PageBlockElementFactory;
use HouseOfAgile\NakaCMSBundle\Factory\PageFactory;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class PageBlockElementFixtures  extends BaseFixture implements DependentFixtureInterface
{

    protected function loadData(ObjectManager $manager)
    {
        PageBlockElementFactory::createMany(6, function () {
            return [
                'page' => PageFactory::random(),
                'blockElement' => BlockElementFactory::random(),
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
