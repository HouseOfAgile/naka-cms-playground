<?php

namespace HouseOfAgile\NakaCMSBundle\DataFixtures;

use HouseOfAgile\NakaCMSBundle\Factory\BlockElementFactory;
use HouseOfAgile\NakaCMSBundle\Factory\BlockElementTypeFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class BlockElementFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        BlockElementTypeFactory::createMany(5);

        
        BlockElementFactory::createMany(6, function () {
            $randomBlockElementType = BlockElementTypeFactory::random();
            return [
                'blockElementType' => $randomBlockElementType,
                'htmlCode' => $randomBlockElementType->getHtmlCode(),
                'cssCode' => $randomBlockElementType->getCssCode(),
                'jsCode' => $randomBlockElementType->getJsCode(),
            ];
        });
        $manager->flush();
    }
}
