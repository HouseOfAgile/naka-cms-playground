<?php

namespace HouseOfAgile\NakaCMSBundle\Component\PageDecorator;

use HouseOfAgile\NakaCMSBundle\Component\ContentDumper\ContentDumper;
use App\Entity\BlockElement;
use HouseOfAgile\NakaCMSBundle\Entity\Page;
use HouseOfAgile\NakaCMSBundle\Entity\StaticPage;
use HouseOfAgile\NakaCMSBundle\Helper\LoggerCommandTrait;
use HouseOfAgile\NakaCMSBundle\Repository\StaticPageRepository;
use Psr\Log\LoggerInterface;

class PageDecorator
{
    use LoggerCommandTrait;

    /** @var LoggerInterface */
    protected $logger;

    /** @var StaticPageRepository */
    protected $staticPageRepository;

    /** @var ContentDumper */
    protected $contentDumper;

    public function __construct(
        LoggerInterface $scrappingLogger,
        StaticPageRepository $staticPageRepository,
        ContentDumper $contentDumper
    ) {
        $this->logger = $scrappingLogger;
        $this->staticPageRepository = $staticPageRepository;
        $this->contentDumper = $contentDumper;
    }

    public function decorateBlockElementsForStaticPage(?StaticPage $staticPage)
    {
        return $staticPage ? $this->decoratePageBlockElements($staticPage->getPageBlockElements()):[];
    }

    public function decorateBlockElementsForPage(Page $page)
    {
        return $this->decoratePageBlockElements($page->getPageBlockElements());
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
