<?php

namespace HouseOfAgile\NakaCMSBundle\Controller;

use HouseOfAgile\NakaCMSBundle\KnpUIpsum;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class PageController extends AbstractController
{

    private $knpUIpsum;

    public function __construct(KnpUIpsum $knpUIpsum)
    {
        $this->knpUIpsum = $knpUIpsum;
    }
    
    /**
     * @Route("/sadsad", name="naka_homepage")
     */
    public function homepage()
    {
        $articleContent = $this->knpUIpsum->getParagraphs();

        dd($articleContent);
        // $homepageStaticPage = $staticPageRepository->findOneBy(['name' => 'homepage']);

        $viewParams = [
            // 'homepageStaticPage' => $homepageStaticPage,
        ];
        // dd($viewParams);
        return $this->render('page/homepage.html.twig', $viewParams);
    }
}
