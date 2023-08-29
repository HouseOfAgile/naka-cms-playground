<?php

namespace HouseOfAgile\NakaCMSBundle\Controller\Admin;

use App\Entity\BlockElement;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CodeEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Routing\Annotation\Route;

class BlockElementCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return BlockElement::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        // $crud = parent::configureCrud($crud);

        return $crud
            ->showEntityActionsInlined(true)
            ->overrideTemplate('crud/index', '@NakaCMS/backend/block-element/list.html.twig');
    }

    public function configureActions(Actions $actions): Actions
    {

        $blockElementId = function (BlockElement $blockElement): array {
            return [
                'blockElement' => $blockElement->getId(),
            ];
        };

        // $routeParameters();
        // dd($routeParameters);
        $editBlockElement = Action::new('editBlockElement', 'Edit Block Element', 'fa fa-pen')
            ->linkToRoute('edit_page_element_redir', $blockElementId)
            ->addCssClass('btn btn-warning text-white');
        return $actions
            ->add(Crud::PAGE_INDEX, $editBlockElement);
    }

    public function configureFields(string $pageName): iterable
    {
        $id = IdField::new('id');
        $panelSetting = FormField::addPanel(
            'backend.form.blockElement.panelSetting'
        )
            ->addCssClass('col-12');

        $name = TextField::new('name');

        $blockElementType = AssociationField::new(
            'blockElementType',
            'backend.form.blockElement.type'
        )
            ->setHelp('backend.form.blockElement.type.help')
            ->setDisabled(true);

        $pageBlockElements = AssociationField::new('pageBlockElements', 'backend.form.page.pageBlockElements')
            ->setFormTypeOption('by_reference', false)
            ->setHelp('backend.form.blockElement.pageBlockElements.help');
        // $createdAt = DateTimeField::new('createdAt')->setTemplatePath('@NakaCMS/naka/backend/fields/common/field_human_date.html.twig');
        // $updatedAt = DateTimeField::new('updatedAt')->setTemplatePath('@NakaCMS/naka/backend/fields/common/field_human_date.html.twig');

        $panelCodeElement = FormField::addPanel(
            'backend.form.blockElement.panelCodeElement'
        )
            ->setHelp('backend.form.blockElement.panelCodeElement.help')
            ->addCssClass('col-12');

        $htmlCode = CodeEditorField::new(
            'htmlCode',
            'backend.form.blockElement.htmlCode'
        );
        $cssCode = CodeEditorField::new(
            'cssCode',
            'backend.form.blockElement.cssCode'
        )->setColumns(6);
        $jsCode = CodeEditorField::new(
            'jsCode',
            'backend.form.blockElement.jsCode'
        )->setColumns(6);

        $gallery = AssociationField::new('gallery')
            ->setHelp('backend.form.blockElement.gallery.help')
            ->setFormTypeOption('required', false);
        $isFluid = BooleanField::new('isFluid')
            ->setHelp('backend.form.blockElement.isFluid.help');

        if (Crud::PAGE_INDEX === $pageName) {
            return [$id, $name, $pageBlockElements, $blockElementType];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return [$id, $name, $blockElementType, $htmlCode, $cssCode, $jsCode, $gallery, $isFluid];
        } elseif (Crud::PAGE_NEW === $pageName) {
            return [$panelSetting, $name, $blockElementType,  $panelCodeElement, $htmlCode, $cssCode, $jsCode, $gallery, $isFluid];
        } elseif (Crud::PAGE_EDIT === $pageName) {
            return [$panelSetting, $name, $blockElementType,  $panelCodeElement, $htmlCode, $cssCode, $jsCode, $gallery, $isFluid];
        }
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    #[Route(path: '/{blockElement}/edit', name: 'edit_page_element_redir')]
    public function editBlockElementRedirection(BlockElement $blockElement): \Symfony\Component\HttpFoundation\Response
    {
        return $this->redirectToRoute('edit_page_element', [
            'blockElement' => $blockElement->getId()
        ]);
    }
}
