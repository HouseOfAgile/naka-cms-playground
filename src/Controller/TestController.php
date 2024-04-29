<?php

namespace HouseOfAgile\NakaCMSBundle\Controller;

use App\Entity\BlockElement;
use Exception;
use HouseOfAgile\NakaCMSBundle\Component\ContentDumper\ContentDumper;
use HouseOfAgile\NakaCMSBundle\Repository\PageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/test', requirements: ['_locale' => 'en|de|fr'], name: 'naka_test_')]
class TestController extends AbstractController
{
    /** @var ContentDumper */
    private $contentDumper;

    public function __construct(ContentDumper $contentDumper)
    {
        $this->contentDumper = $contentDumper;
    }

    #[Route(path: '/{_locale<%app.supported_locales%>}/simple', name: 'simple')]
    public function simpleTest(PageRepository $pageRepository): Response
    {
        $testBlockElement = new BlockElement();
        $testBlockElement->setJsCode('
        <img src="%% \'picture-18\' %%" class="img-fluid" alt="menu image">
        ');
        $testBlockElement->setHtmlCode('
        asfdsfdsf sd fds fds fds fds fds fsd fdsf
        sdfsdflkhdsfkjhdsfkjhdsfk sdkhf ksdjhfkjdshf kjsdh fkjhds fkj sdkjhf kjds fkjsdh fdsf
         sdlksd flkhs dfkh dsf

        %% picture-98 %%
        %% picture-2 %%
        %% picture--2 %%
        %% picture2 %%
        %% link("page_view", { "slug": "asd" }) %%
        %% link("app_contact") %%
        %% link() %%
        %% link %%
        %% button("app_contact") %%
        ');


        // %% link("app_contact", 56, "sfdsdf", 56) %%

        $decoratedBlockElements[] = $this->contentDumper->decorateBlockElement($testBlockElement);

        dd($decoratedBlockElements);
        $viewParams = [
            'decoratedBlockElements' => $decoratedBlockElements,
        ];
        return $this->render('@NakaCMS/naka/test/show.html.twig', $viewParams);
    }
}
