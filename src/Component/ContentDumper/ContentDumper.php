<?php

namespace HouseOfAgile\NakaCMSBundle\Component\ContentDumper;

use Doctrine\ORM\EntityManagerInterface;
use HouseOfAgile\NakaCMSBundle\Entity\BlockElement;
use HouseOfAgile\NakaCMSBundle\Helper\LoggerCommandTrait;
use HouseOfAgile\NakaCMSBundle\Repository\PictureRepository;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Liip\ImagineBundle\Imagine\Data\DataManager;
use Liip\ImagineBundle\Imagine\Filter\FilterManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
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

    /** @var UrlGeneratorInterface */
    private $router;

    /** @var FilterManager */
    private $filterManager;

    /** @var VichUploaderHelper */
    protected $vichUploaderHelper;

    protected $projectDir;
    protected $devMode;

    public function __construct(
        LoggerInterface $scrappingLogger,
        PictureRepository $pictureRepository,
        TranslatorInterface $translator,
        VichUploaderHelper $vichUploaderHelper,
        CacheManager $cacheManager,
        DataManager $dataManager,
        FilterManager $filterManager,
        UrlGeneratorInterface $router,
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
        $this->router = $router;
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
     * manage dynamic content
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

        // $dataString = $this->replacePictureOld($dataString);
        preg_match_all('!%%([\s]?[^%].*)%%!', $dataString, $dynamicMatches);
        if (!empty(array_filter($dynamicMatches))) {
            foreach ($dynamicMatches[0] as $id => $linkMatch) {

                // $generatedUrl = $this->router->generate('sign_up');
                // dump('checking ' . $id);
                // dump($dynamicMatches[0][$id]);
                if (strpos($dynamicMatches[1][$id], 'picture') !== false) {
                    $replacedData = $this->replacePicture($dynamicMatches[1][$id]);
                } elseif (strpos($dynamicMatches[1][$id], 'link') !== false) {
                    $replacedData = $this->replaceLink($dynamicMatches[1][$id]);
                }

                    $dataString = str_replace($dynamicMatches[0][$id], !empty($replacedData) ? $replacedData : '', $dataString);
                // if (false) {
                //     // if ($thisUrl) {
                //     $matchRegexp = sprintf(
                //         '!%%%%[\s]?\'?picture-%s%s\'?[\s]?%%%%!',
                //         $pictureMatches[1][$id],
                //         !empty($pictureMatches[2][$id]) ? '\|' . $pictureMatches[2][$id] : ''
                //     );
                //     $dataString = preg_replace($matchRegexp, $assetPath,  $dataString);
                // }
            }
        }


        // preg_match_all('!%%[\s]?\'?(link|button)\(([^\'|\)]*)\)\'?[\s]?%%!', $dataString, $dynamicMatches);

        // if (!empty(array_filter($dynamicMatches))) {
        //     foreach ($dynamicMatches[0] as $id => $linkMatch) {

        //         // $generatedUrl = $this->router->generate('sign_up');
        //         // dump('checking ' . $id);
        //         $thisUrl = $dynamicMatches[1][$id];
        //         // dump($thisUrl );

        //         if (false) {
        //         // if ($thisUrl) {
        //             $matchRegexp = sprintf(
        //                 '!%%%%[\s]?\'?picture-%s%s\'?[\s]?%%%%!',
        //                 $pictureMatches[1][$id],
        //                 !empty($pictureMatches[2][$id]) ? '\|' . $pictureMatches[2][$id] : ''
        //             );
        //             $dataString = preg_replace($matchRegexp, $assetPath,  $dataString);
        //         }
        //     }
        // }
        return $dataString;
    }

    public function replacePicture($stringToReplace): string
    {
        preg_match('!\'?picture-([^\'|\||%]*)\|?([^\'|%]*)\'?!', $stringToReplace, $pictureMatch);

        if (is_array($pictureMatch) && array_key_exists(1, $pictureMatch) && is_int((int)$pictureMatch[1])) {
            $picture = $this->pictureRepository->find($pictureMatch[1]);

            if ($this->devMode && !$picture) {
                $picture = $this->pictureRepository->getRandomPicture();
            }

            if ($picture) {
                $realAssetPath = $this->vichUploaderHelper->asset($picture, 'imageFile');
                $filter = $pictureMatch[2] ?? null;
                $assetPath = $this->getFilteredImage($realAssetPath, $filter);
            } else {
                $assetPath = '/asset/broken-image.png';
            }

            $matchRegexp = sprintf(
                '![\s]?\'?picture-%s%s\'?[\s]?!',
                $pictureMatch[1],
                !empty($pictureMatch[2]) ? '\|' . $pictureMatch[2] : ''
            );

            return $assetPath;
            // $dataString = preg_replace($matchRegexp, $assetPath,  $dataString);
        } else {
            $this->logger->info(sprintf(
                'bad picture key: %s',
                $stringToReplace
            ));            return '';
        }
    }

    /**
     * 
     * replace %% link("page_view", {"slug": "sub-seminar-house"}) %% with a route from the project
     *
     * @param [type] $stringToReplace
     * @return string
     */
    public function replaceLink($stringToReplace): string
    {
        preg_match_all(
            '@(?:(link)\(|\G(?!^),\s*)\K(?:"[^"]*"|[^,()]+)(?=[^()]*\))@',
            $stringToReplace,
            $linkMatch
        );
        // strpos($dynamicMatches[1][$id], 'picture')
        if (is_array($linkMatch) && array_key_exists(0, $linkMatch[0])) {
            // we clean double quotes here
            $routeName = str_replace(['"', "'"], "", $linkMatch[0][0]);
            // We add route paramters if defined 
            $routeParameters = array_key_exists(1, $linkMatch[0]) ? json_decode($linkMatch[0][1], TRUE) : [];
            $generatedUrl = $this->router->generate(
                $routeName,
                is_array($routeParameters) ? $routeParameters : []
            );

            return $generatedUrl;
        } else {
            $this->logger->info(sprintf(
                'bad link key: %s',
                $stringToReplace
            ));
            return '';
        }
    }

    public function replacePictureOld($dataString)
    {

        // add images
        // manage filter / improve regexp here
        preg_match_all('!%%[\s]?\'?picture-([^\'|\||%]*)\|?([^\'|%]*)\'?[\s]?%%!', $dataString, $pictureMatches);

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
            dd($th);
            dd($filter);
        }

        return $this->cacheManager->resolve($path, $filter);
    }
}
