<?php

namespace HouseOfAgile\NakaCMSBundle\Component\ContentManagement;

use App\Entity\BlockElement;
use App\Entity\BlockElementType;
use App\Entity\Menu;
use App\Entity\MenuItem;
use App\Entity\Page;
use App\Entity\PageBlockElement;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use HouseOfAgile\NakaCMSBundle\DBAL\Types\NakaMenuItemType;
use HouseOfAgile\NakaCMSBundle\Repository\BlockElementRepository;
use HouseOfAgile\NakaCMSBundle\Repository\MenuItemRepository;
use HouseOfAgile\NakaCMSBundle\Repository\MenuRepository;
use HouseOfAgile\NakaCMSBundle\Repository\PageBlockElementRepository;

class BlockManager
{

    protected const TRANSLATION_COMMON_KEYS = ['thisBlockElement'];

    /** @var EntityManagerInterface */
    protected $entityManager;

    /** @var BlockElementRepository */
    protected $blockElementRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        BlockElementRepository $blockElementRepository
    ) {
        $this->entityManager = $entityManager;
        $this->blockElementRepository = $blockElementRepository;
    }

    public function createBlockElementFromBlockElementType(BlockElementType $blockElementType, $name): BlockElement
    {
        $newBlockElement = $this->copyFromBlockElementType($blockElementType, $name);
        $this->entityManager->persist($newBlockElement);
        $this->entityManager->flush();
        return $newBlockElement;
    }

    public function copyFromBlockElementType(BlockElementType $blockElementType, $name = ''): BlockElement
    {
        $newBlockElement = new BlockElement;
        $newBlockElement->setName($name);
        $newBlockElement->setBlockElementType($blockElementType);
        $renamedKey = $name ? lcfirst(str_replace(['_', ' '], '', ucwords($name))) : null;
        return $this->renameDynamicKeysForBlockElementType($newBlockElement, $blockElementType, $renamedKey);
    }

    public function renameDynamicKeysForBlockElementType(BlockElement $blockElement, BlockElementType $blockElementType = null, string $renamedKey = null)
    {
        $htmlCode = $this->renameDynamicKeys($blockElementType->getHtmlCode(), $renamedKey);
        $blockElement->setHtmlCode($htmlCode);
        $cssCode = $this->renameDynamicKeys($blockElementType->getCssCode(), $renamedKey);
        $blockElement->setCssCode($cssCode);
        $jsCode = $this->renameDynamicKeys($blockElementType->getjsCode(), $renamedKey);
        $blockElement->setJsCode($jsCode);
        return $blockElement;
    }

    private function renameDynamicKeys(string $content = null, string $renamedKey = null)
    {
        if ($renamedKey == null) {
            return $content;
        }
        preg_match_all('!{{[\s]?\'(.*)\'[\s]?}}!', $content, $translationKeys);
        $uniqueTranslationKeys = array_unique($translationKeys[1]);
        foreach ($uniqueTranslationKeys as $translationKey) {
            // here we replace the common part with the renamedKey
            $updatedTranslationKey = str_replace(self::TRANSLATION_COMMON_KEYS, $renamedKey, $translationKey);
            $content = preg_replace('!{{[\s]?\'' . $translationKey . '\'[\s]?}}!',  '{{ \'' . $updatedTranslationKey . '\' }}', $content);
        }
        return $content;
    }

    public function renameSlugForAllBlocks(string $searchedSlugKey, string $renamedSlugKey = null)
    {
        foreach ($this->blockElementRepository->findAll() as $blockElement) {
            $this->renameSlugKey($blockElement->getHtmlCode(), $searchedSlugKey, $renamedSlugKey);
            $this->renameSlugKey($blockElement->getJsCode(), $searchedSlugKey, $renamedSlugKey);
            $this->renameSlugKey($blockElement->getCssCode(), $searchedSlugKey, $renamedSlugKey);
        }
    }



    public function renameSlugKey(string $content = null, string $searchedSlugKey, string $renamedSlugKey = null)
    {
        if ($searchedSlugKey == null || $content == null || strpos($content, $searchedSlugKey) === false) {
            return $content;
        }
        dump($content);
        preg_match_all('!{\"slug\"\:[\s]?\"(.*)\"[\s]?}!', $content, $translationKeys);
        $uniqueTranslationKeys = array_unique($translationKeys[1]);
        foreach ($uniqueTranslationKeys as $translationKey) {
            if ($translationKey == $searchedSlugKey) {
                dump($translationKey);
                $content = preg_replace('!{\"slug\"\:[\s]?"'. $translationKey . '"[\s]?}!',  '{"slug": "' . $renamedSlugKey . '" }', $content);
            }
        }
        dd($content);
        return $content;
    }
}
