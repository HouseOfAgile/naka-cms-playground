<?php

namespace HouseOfAgile\NakaCMSBundle\Controller;

use Exception;
use HouseOfAgile\NakaCMSBundle\Component\PageDecorator\PageDecorator;
use HouseOfAgile\NakaCMSBundle\DBAL\Types\NakaPageType;
use HouseOfAgile\NakaCMSBundle\Entity\Page;
use HouseOfAgile\NakaCMSBundle\Repository\PageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/page", requirements={"_locale": "en|de|fr"}, name="page_")
 */
class PageController extends AbstractController
{
    /** @var PageDecorator */
    private $pageDecorator;

    public function __construct(PageDecorator $pageDecorator)
    {
        $this->pageDecorator = $pageDecorator;
    }
    /**
     * @Route("/{_locale<%app.supported_locales%>}/pages", name="list")
     */
    public function showList(PageRepository $pageRepository): Response
    {
        $pages = $pageRepository->findAll();


        $viewParams = [
            'pages' => $pages,
        ];
        return $this->render('@NakaCMS/naka/page/show-list.html.twig', $viewParams);
    }

    /**
     * @Route("/{_locale<%app.supported_locales%>}/{slug}", name="view")
     */
    public function showPage(
        Page $page
    ): \Symfony\Component\HttpFoundation\Response {

        $viewParams = [
            'page' => $page,
            'blockElements' => $this->pageDecorator->decorateBlockElementsForPage($page),
        ];
        // show generatorName only on some pages
        if (in_array(strtolower($page->getSlug()), ['impressum', 'privacy-policy'])){
            $viewParams['trademarkOn'] = true;
        }
        switch ($page->getPageType()) {
            case NakaPageType::PAGE_TYPE_BLOCKS_ONLY:
                return $this->render('@NakaCMS/naka/page/show-page.html.twig', $viewParams);
                break;
            case NakaPageType::PAGE_TYPE_CONTENT_ONLY:
                return $this->render('@NakaCMS/naka/page/show-static-page.html.twig', $viewParams);
                break;
            
            default:
                # code...
                throw new Exception('Not supported');
                break;
        }
    }
}
