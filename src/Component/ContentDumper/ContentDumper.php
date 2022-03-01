<?php

namespace HouseOfAgile\NakaCMSBundle\Component\ContentDumper;

use HouseOfAgile\NakaCMSBundle\Entity\BlockElement;
use HouseOfAgile\NakaCMSBundle\Helper\LoggerCommandTrait;
use HouseOfAgile\NakaCMSBundle\Repository\PictureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Liip\ImagineBundle\Imagine\Data\DataManager;
use Liip\ImagineBundle\Imagine\Filter\FilterManager;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper as VichUploaderHelper;


class ContentDumper
{
    use LoggerCommandTrait;

    /** @var LoggerInterface */
    protected $logger;

    /** @var TranslatorInterface */
    protected $translator;

    /** @var PictureRepository */
    protected $pictureRepository;

    /** @var EntityManagerInterface */
    protected $entityManager;

    /** @var CacheManager */
    private $cacheManager;

    /** @var DataManager */
    private $dataManager;

    /** @var FilterManager */
    private $filterManager;

    /** @var VichUploaderHelper */
    protected $vichUploaderHelper;

    protected $projectDir;
    protected $devModedevMode;

    public function __construct(
        LoggerInterface $scrappingLogger,
        PictureRepository $pictureRepository,
        TranslatorInterface $translator,
        VichUploaderHelper $vichUploaderHelper,
        CacheManager $cacheManager,
        DataManager $dataManager,
        FilterManager $filterManager,
        $projectDir,
        $devMode
    ) {
        $this->logger = $scrappingLogger;
        $this->pictureRepository = $pictureRepository;
        $this->translator = $translator;
        $this->vichUploaderHelper = $vichUploaderHelper;
        $this->projectDir = $projectDir;
        $this->cacheManager  = $cacheManager;
        $this->dataManager   = $dataManager;
        $this->filterManager = $filterManager;
        $this->devMode = $devMode;

        // $this->entityManager = $entityManager;
    }


    public function decorateBlockElement(BlockElement $blockElement)
    {
        return [
            'blockElement' => $blockElement,
            'htmlCode' => $this->decorateString($blockElement->getHtmlCode()),
            'cssCode' => $this->decorateString($blockElement->getCssCode()),
            'jsCode' => $this->decorateString($blockElement->getJsCode()),
        ];
    }

    /**
     * manage translation
     *
     * @param string $dataString
     * @return void
     */
    public function decorateString(?string $dataString)
    {
        // Replace genId
        preg_match_all('!{{[\s]?\'genid\'[\s]?}}!', $dataString, $translationKeys);
        if (count($translationKeys) > 1) {
            $uniqueTranslationKeys = array_unique($translationKeys[1]);
            foreach ($uniqueTranslationKeys as $translationKey) {
                $dataString = preg_replace('!{{[\s]?\'' . $translationKey . '\'[\s]?}}!',  'uniqId-' . rand(0, 9999), $dataString);
            }
        }

        // Replace text translation
        preg_match_all('!{{[\s]?\'(.*)\'[\s]?}}!', $dataString, $translationKeys);
        $uniqueTranslationKeys = array_unique($translationKeys[1]);
        foreach ($uniqueTranslationKeys as $translationKey) {
            $dataString = preg_replace('!{{[\s]?\'' . $translationKey . '\'[\s]?}}!',  $this->translator->trans($translationKey), $dataString);
        }

        // add images
        // manage filter / improve regexp here
        // preg_match_all('!%%[\s]?\'picture-(.*)-?(.*)?\'[\s]?%%!', $dataString, $pictureMatches);
        preg_match_all('!%%[\s]?\'?picture-([^\'|\||%]*)\|?([^\'|%]*)\'?[\s]?%%!', $dataString, $pictureMatches);

        // if (!empty(array_filter($pictureMatches))) {
        //     $pictureIds = array_unique($pictureMatches[1]);
        //     dump($pictureMatches);
        //     foreach ($pictureIds as $pictureId) {
        //         $picture = $this->pictureRepository->find($pictureId);
        //         $realPathAsset = $this->vichUploaderHelper->asset($picture, 'imageFile');
        //         $filter = count($pictureMatches) > 2 && !empty($pictureMatches[2][0]) ? $pictureMatches[2] : null;
        //         $pathAsset = $this->getFilteredImage($realPathAsset, $filter);
        //         $dataString = preg_replace('!%%[\s]?\'picture-' . $pictureId . '\'[\s]?%%!', $pathAsset, $dataString);
        //     }
        // }


        if (!empty(array_filter($pictureMatches))) {
            foreach ($pictureMatches[0] as $id => $pictureMatch) {
                // dump('checking ' . $id);
                $picture = $this->pictureRepository->find($pictureMatches[1][$id]);

                if ($this->devMode && !$picture) {
                    $picture = $this->pictureRepository->getRandomPicture();
                }
                
                if ($picture) {
                    $realAssetPath = $this->vichUploaderHelper->asset($picture, 'imageFile');
                    $filter = $pictureMatches[2][$id] ?? null;
                    $assetPath = $this->getFilteredImage($realAssetPath, $filter);
                } else {
                    $assetPath = '/asset/broken-image.png';
                }
                $matchRegexp = sprintf(
                    '!%%%%[\s]?\'?picture-%s%s\'?[\s]?%%%%!',
                    $pictureMatches[1][$id],
                    !empty($pictureMatches[2][$id]) ? '\|' . $pictureMatches[2][$id] : ''
                );
                $dataString = preg_replace($matchRegexp, $assetPath,  $dataString);
            }
        }

        // remove remaining unchanged dynamic content
        $matchRegexp = sprintf(
            '!%%%%[\s]?\'?picture-([^\'|\||%%]*)\|?([^\'|%%]*)\'?[\s]?%%%%!',
        );
        return $dataString;
    }

    public function getFilteredImage($path, $filter)
    {
        // @todo fix local url here
        // if (empty($filter)) return "http://localhost:11080" . $path;
        if (empty($filter)) return $path;
        try {
            if (!$this->cacheManager->isStored($path, $filter)) {
                $binary = $this->dataManager->find($filter, $path);

                $filteredBinary = $this->filterManager->applyFilter($binary, $filter, [
                    // 'filters' => [
                    //     'thumbnail' => [
                    //         'size' => [$width, $height]
                    //     ]
                    // ]
                ]);

                $this->cacheManager->store($filteredBinary, $path, $filter);
            }
        } catch (\Throwable $th) {
            dd($filter);
        }

        return $this->cacheManager->resolve($path, $filter);
    }
}
