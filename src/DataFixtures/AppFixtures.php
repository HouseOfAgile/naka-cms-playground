<?php

namespace HouseOfAgile\NakaCMSBundle\DataFixtures;

use Doctrine\Persistence\ObjectManager;

class AppFixtures extends BaseFixture
{
    public function loadData(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);

        $manager->flush();
    }
}

