<?php

namespace HouseOfAgile\NakaCMSBundle\Controller;

use App\Entity\BlockElement;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use HouseOfAgile\NakaCMSBundle\Component\ContentDumper\ContentDumper;
use HouseOfAgile\NakaCMSBundle\Component\ContentManagement\PageBlockManager;
use HouseOfAgile\NakaCMSBundle\Controller\Admin\BlockElementCrudController;
use HouseOfAgile\NakaCMSBundle\Form\BlockElementType;
use HouseOfAgile\NakaCMSBundle\Form\ChooseBlockElementTypeType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\ClickableInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/block-element')]
class BlockElementAdminController extends AbstractController
{

    /** @var AdminUrlGenerator */
    protected $adminUrlGenerator;


    public function __construct(
        AdminUrlGenerator $adminUrlGenerator
    ) {
        $this->adminUrlGenerator = $adminUrlGenerator;
    }

    #[Route(path: '/choose-block-element-type', name: 'choose_page_element_type')]
    public function chooseBlockElementType(
        Request $request,
        PageBlockManager $pageBlockManager
    ): Response {
        $form = $this->createForm(ChooseBlockElementTypeType::class, null, [
            'add_submit' => true,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $data = $form->getData();
                $newBlockElement = $pageBlockManager->createBlockElementFromBlockElementType(
                    $data['blockElementType'],
                    $data['name']
                );
                $this->addFlash('success', sprintf('Block Element %s has been created!', $newBlockElement->getId()));
                return $this->redirToAction();
            } else {
                $this->addFlash('danger', sprintf('There is an error while creating this BlockElement!'));
            }
        }
        $viewParams = [
            'form' => $form->createView(),
            'blockElementType' => [
                'cssCode' => true,
                'htmlCode' => true,
                'jsCode' => true,
                'type' => 'carousel',
            ],
        ];
        // return $this->render('naka/pages/show-block-element-type.html.twig', $viewParams);

        return $this->render('@NakaCMS/backend/topic/block-element/choose_block_element_type.html.twig', $viewParams);
    }

    #[Route(path: '/block-element/{blockElement}/edit', name: 'edit_page_element')]
    public function editBlockElement(
        BlockElement $blockElement,
        ContentDumper $contentDumper,
        EntityManagerInterface $em,
        Request $request
    ): Response {
        $form = $this->createForm(BlockElementType::class, $blockElement, [
            'add_refresh' => true,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $blockElement = $form->getData();
                /** @var ClickableInterface $refreshBtn  */
                $refreshBtn = $form->get("refresh");
                if ($refreshBtn->isClicked()) {
                    // dd($blockElement);

                } else {
                    $em->persist($blockElement);
                    $em->flush();
                    $this->addFlash('success', sprintf('Block Element %s has been updated!', $blockElement->getId()));
                    return $this->redirToAction();
                }
            } else {
                $this->addFlash('danger', sprintf('There was an error while updating this BlockElement!'));
            }
        }
        $viewParams = [
            'form' => $form->createView(),
            'blockElement' => $blockElement,
            'decoratedBlockElement' => $contentDumper->decorateBlockElement($blockElement),
        ];
        // dd( $viewParams);

        return $this->render('@NakaCMS/backend/topic/block-element/edit_block_element.html.twig', $viewParams);
    }


    #[Route(path: '/block-element/{blockElement}/decorated', name: 'get_decorated_page_element')]
    public function getDecoratedBlockElementJson(
        BlockElement $blockElement,
        ContentDumper $contentDumper,
        Request $request
    ): Response {
        $decoratedBlockElement = $contentDumper->decorateBlockElement($blockElement);
        return $this->json($decoratedBlockElement);
    }

    #[Route(path: '/decorate', name: 'decorate_string', methods: 'POST')]
    public function decorateStringJson(
        ContentDumper $contentDumper,
        Request $request
    ): Response {
        $parameters = json_decode($request->getContent(), true);
        $stringToDecorate = $parameters['stringToDecorate'];
        $decoratedString = $contentDumper->decorateString($stringToDecorate);
        return $this->json(['decoratedString' => $decoratedString]);
    }

    /**
     * Simple redir to index
     *
     * @param string $action
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    private function redirToAction(string $action = Action::INDEX): \Symfony\Component\HttpFoundation\RedirectResponse
    {
        return $this->redirect($this->adminUrlGenerator
            ->setController(BlockElementCrudController::class)
            ->setAction($action)
            ->generateUrl());
    }
}
