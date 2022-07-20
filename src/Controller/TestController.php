<?php

namespace HouseOfAgile\NakaCMSBundle\Controller;

use App\Entity\BlockElement;
use Exception;
use HouseOfAgile\NakaCMSBundle\Component\ContentDumper\ContentDumper;
use HouseOfAgile\NakaCMSBundle\Component\ContentManagement\PageBlockManager;
use HouseOfAgile\NakaCMSBundle\Component\PageDecorator\PageDecorator;
use HouseOfAgile\NakaCMSBundle\DBAL\Types\NakaPageType;
use HouseOfAgile\NakaCMSBundle\Entity\Page;
use HouseOfAgile\NakaCMSBundle\Repository\PageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/test", requirements={"_locale": "en|de|fr"}, name="naka_test_")
 */
class TestController extends AbstractController
{
    /** @var ContentDumper */
    private $contentDumper;

    public function __construct(ContentDumper $contentDumper)
    {
        $this->contentDumper = $contentDumper;
    }

    /**
     * @Route("/{_locale<%app.supported_locales%>}/simple", name="simple")
     */
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

    /**
     * @Route("/{_locale<%app.supported_locales%>}/one-time", name="one_time")
     */
    public function oneTimeTest(PageBlockManager $pageBlockManager): Response
    {
        $pageBlockManager->renameSlugForAllBlocks('oral-hygiene-and-professional-teeth-cleaning','oral-hygiene-and-professional-teeth-cleaning2');
        $viewParams = [
        ];
        return $this->render('@NakaCMS/naka/test/show.html.twig', $viewParams);
    }
}
