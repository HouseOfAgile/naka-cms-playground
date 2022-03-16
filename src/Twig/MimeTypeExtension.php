<?php

namespace HouseOfAgile\NakaCMSBundle\Twig;

use Carbon\Carbon;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Vich\UploaderBundle\Entity\File as EmbeddedFile;

class MimeTypeExtension extends AbstractExtension
{

    public function getFilters()
    {
        return [
            new TwigFilter('isAnImage', [$this, 'isAnImage']),
        ];
    }

    public function isAnImage(EmbeddedFile $file)
    {
        return $file->getMimeType() == 'image/jpeg'
            || $file->getMimeType() == 'image/png'
            || $file->getMimeType() == 'image/bmp';
    }
}
