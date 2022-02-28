<?php

namespace HouseOfAgile\NakaCMSBundle\DataFixtures;

use HouseOfAgile\NakaCMSBundle\Service\UploaderHelper;
use Doctrine\Persistence\ObjectManager;

class PageGalleryFixtures extends BaseFixture
{
    public function __construct(UploaderHelper $uploaderHelper)
    {
        $this->uploaderHelper = $uploaderHelper;
    }
    public function loadData(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);

        // $fixtureImageFile = $this->getFakeUploadImage();
        // $fixtureImageFileName = $this->uploaderHelper
        //     ->uploadPagePicture($fixtureImageFile);
        // $srcImage = __DIR__ . "/../../public/uploads/page_picture/" . $fixtureImageFileName;

        // $uploadedFile = new UploadedFile($srcImage, $fixtureImageFileName, null, null, true);


        // PictureFactory::createMany(20);
        // // create 50 Post's
        // PageGalleryFactory::createMany(10, function() {
        //     return [
        //         'images' => PictureFactory::randomRange(2, 6),
        //     ];
        // });

        $manager->flush();
    }

}
