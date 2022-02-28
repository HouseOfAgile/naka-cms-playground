<?php

namespace HouseOfAgile\NakaCMSBundle\Service;

use Gedmo\Sluggable\Util\Urlizer;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Asset\Context\RequestStackContext;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploaderHelper
{
    const PAGE_PICTURE = 'page_picture';
    private string $uploadsPath;
    private $requestStackContext;


    /**
     *
     * @param string $uploadsPath
     * @param RequestStackContext $requestStackContext
     */
    public function __construct(string $uploadsPath, RequestStackContext $requestStackContext)
    {
        $this->uploadsPath = $uploadsPath;
        $this->requestStackContext = $requestStackContext;
    }

    public function uploadPicture(File $file, $picturePath = self::PAGE_PICTURE): string
    {
        $destination = $this->uploadsPath . '/' . $picturePath;
        if ($file instanceof UploadedFile) {
            $originalFilename = $file->getClientOriginalName();
        } else {
            $originalFilename = $file->getFilename();
        }
        $newFilename = Urlizer::urlize(pathinfo($originalFilename, PATHINFO_FILENAME)) . '-' . uniqid() . '.' . $file->guessExtension();
        $file->move(
            $destination,
            $newFilename
        );
        return $destination . '/' . $newFilename;
    }
    public function getPublicPath(string $path): string
    {
        // needed if you deploy under a subdirectory
        return $this->requestStackContext
            ->getBasePath() . '/uploads/' . $path;
    }
}
