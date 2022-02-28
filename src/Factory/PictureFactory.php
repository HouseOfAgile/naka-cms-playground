<?php

namespace HouseOfAgile\NakaCMSBundle\Factory;

use HouseOfAgile\NakaCMSBundle\Entity\Picture;
use HouseOfAgile\NakaCMSBundle\Repository\PictureRepository;
use HouseOfAgile\NakaCMSBundle\Service\UploaderHelper;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @method static Picture|Proxy createOne(array $attributes = [])
 * @method static Picture[]|Proxy[] createMany(int $number, $attributes = [])
 * @method static Picture|Proxy findOrCreate(array $attributes)
 * @method static Picture|Proxy random(array $attributes = [])
 * @method static Picture|Proxy randomOrCreate(array $attributes = [])
 * @method static Picture[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static Picture[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static PictureRepository|RepositoryProxy repository()
 * @method Picture|Proxy create($attributes = [])
 */
final class PictureFactory extends ModelFactory
{

    private static $oldRandomImages = [
        'Quercus_robur.jpg',
        'Quercus_calliprinos.jpg',
        'Cork_oak_trunk_section.jpg',
    ];

    protected $randomImagesFiles;
    protected $imageDirectory;
    protected $randomImages;
    protected $uploaderHelper;


    public function __construct(
        UploaderHelper $uploaderHelper
    ) {
        parent::__construct();
        $this->uploaderHelper = $uploaderHelper;
        $this->imageDirectory = __DIR__ . '/../DataFixtures/images/';
        $this->randomImages = $this->getListOfImages($this->imageDirectory);
        // TODO inject services if required (https://github.com/zenstruck/foundry#factories-as-services)
    }
    protected function getListOfImages(string $directory): array
    {
        $finder = Finder::create()
            ->in($directory)
            ->depth(0)
            ->name(['*.jpg', '*.png'])
            ->sortByName();

        return  array_values(array_map(function ($file) {
            return $file->getRelativePathname();
        }, iterator_to_array($finder)));
    }

    protected function getDefaults(): array
    {

        return [
            'imageFile' => $this->getRandomImage(),
            // TODO add your default values here (https://github.com/zenstruck/foundry#model-factories)
        ];
    }

    protected function initialize(): self
    {


        // see https://github.com/zenstruck/foundry#initialization
        return $this
            // ->afterInstantiate(function(Picture $picture) {})
        ;
    }

    protected static function getClass(): string
    {
        return Picture::class;
    }

    protected function getRandomImage()
    {
        // dump($this->randomImagesFiles);

        $randomImage = $this->randomImages[array_rand($this->randomImages)];
        $fs = new Filesystem();
        $targetPath = sys_get_temp_dir() . '/' . $randomImage;
        $fs->copy($this->imageDirectory . $randomImage, $targetPath, true);
        $fixtureImageFile =  new File($targetPath);

        $fixtureImageFileFullPath = $this->uploaderHelper
            ->uploadPicture($fixtureImageFile, UploaderHelper::PAGE_PICTURE);
        // $srcImage = __DIR__ . "/../../public/uploads/page_picture/" . $fixtureImageFileName;

        return new UploadedFile($fixtureImageFileFullPath, basename($fixtureImageFileFullPath), null, null, true);

        // $this->randomImagesFiles[] = new UploadedFile($targetPath, $oneImage, null, null, true);
    }
}
