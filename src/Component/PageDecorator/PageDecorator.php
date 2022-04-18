<?php

namespace HouseOfAgile\NakaCMSBundle\Component\PageDecorator;

use HouseOfAgile\NakaCMSBundle\Component\ContentDumper\ContentDumper;
use App\Entity\BlockElement;
use HouseOfAgile\NakaCMSBundle\Entity\Page;
use HouseOfAgile\NakaCMSBundle\Helper\LoggerCommandTrait;
use Psr\Log\LoggerInterface;

class PageDecorator
{
    use LoggerCommandTrait;

    /** @var LoggerInterface */
    protected $logger;

    /** @var ContentDumper */
    protected $contentDumper;

    public function __construct(
        LoggerInterface $scrappingLogger,
        ContentDumper $contentDumper
    ) {
        $this->logger = $scrappingLogger;
        $this->contentDumper = $contentDumper;
    }

    public function decorateBlockElementsForPage(Page $page)
    {
        return $page  ? $this->decoratePageBlockElements($page->getPageBlockElements()):[];
    }

    public function decoratePageBlockElements($pageBlockElements)
    {
        $decoratedBlockElements = [];
        foreach ($pageBlockElements as $pageBlockElement) {
            $decoratedBlockElements[] = $this->contentDumper->decorateBlockElement($pageBlockElement->getBlockElement());
        }
        return $decoratedBlockElements;
    }
}
