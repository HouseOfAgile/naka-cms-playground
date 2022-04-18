<?php

namespace HouseOfAgile\NakaCMSBundle\Controller;

use HouseOfAgile\NakaCMSBundle\Component\PageDecorator\PageDecorator;
use HouseOfAgile\NakaCMSBundle\Repository\PageRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NakaController extends BaseController
{
    /** @var PageDecorator */
    private $pageDecorator;

    public function __construct(PageDecorator $pageDecorator)
    {
        $this->pageDecorator = $pageDecorator;
    }

    /**
     * @Route("/")
     */
    public function indexNoLocale(): Response
    {
        return $this->redirectToRoute('app_homepage', ['_locale' => 'en']);
    }

    /**
     * @Route("/{_locale<%app.supported_locales%>}/", name="app_homepage")
     */
    public function homepage(
        PageRepository $pageRepository
    ) {
        $homePage = $pageRepository->findOneBy(['name' => 'homepage']);
        $viewParams = [
            'homePage' => $homePage,
            'blockElements' => $this->pageDecorator->decorateBlockElementsForPage($homePage),
        ];
        return $this->render('@NakaCMS/naka/homepage.html.twig', $viewParams);
    }
}
