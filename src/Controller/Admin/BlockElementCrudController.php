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
        return $crud
            ->showEntityActionsAsDropdown(false)
            ->overrideTemplate('crud/index', '@NakaCMS/naka/backend/block-element/list.html.twig');
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
            ->addCssClass('text-warning');
        return $actions
            ->add(Crud::PAGE_INDEX, $editBlockElement);
    }

    public function configureFields(string $pageName): iterable
    {
        $id = IdField::new('id');
        $name = TextField::new('name');
        $blockElementType = AssociationField::new(
            'blockElementType',
            'form.blockElement.type'
        );

        // $createdAt = DateTimeField::new('createdAt')->setTemplatePath('@NakaCMS/naka/backend/fields/common/field_human_date.html.twig');
        // $updatedAt = DateTimeField::new('updatedAt')->setTemplatePath('@NakaCMS/naka/backend/fields/common/field_human_date.html.twig');

        $panelCodeElement = FormField::addPanel(
            'form.blockElement.codeElement'
        )
            ->setHelp('form.blockElement.codeElement.help')
            ->addCssClass('col-12');

        $htmlCode = CodeEditorField::new(
            'htmlCode',
            'form.blockElement.htmlCode'
        );
        $cssCode = CodeEditorField::new(
            'cssCode',
            'form.blockElement.cssCode'
        )->setColumns(6);
        $jsCode = CodeEditorField::new(
            'jsCode',
            'form.blockElement.jsCode'
        )->setColumns(6);

        $gallery = AssociationField::new('gallery');
        $isFluid = BooleanField::new('isFluid');

        if (Crud::PAGE_INDEX === $pageName) {
            return [$id, $name, $blockElementType];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return [$id, $name, $blockElementType, $htmlCode, $cssCode, $jsCode, $gallery, $isFluid];
        } elseif (Crud::PAGE_NEW === $pageName) {
            return [$name, $blockElementType,  $panelCodeElement, $htmlCode, $cssCode, $jsCode, $gallery, $isFluid];
        } elseif (Crud::PAGE_EDIT === $pageName) {
            return [$name, $blockElementType,  $panelCodeElement, $htmlCode, $cssCode, $jsCode, $gallery, $isFluid];
        }
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Route("/{blockElement}/edit", name="edit_page_element_redir")
     */
    public function editBlockElementRedirection(BlockElement $blockElement): \Symfony\Component\HttpFoundation\Response
    {
        return $this->redirectToRoute('edit_page_element', [
            'blockElement' => $blockElement->getId()
        ]);
    }
}
