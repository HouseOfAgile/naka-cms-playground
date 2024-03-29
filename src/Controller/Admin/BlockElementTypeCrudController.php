<?php

namespace HouseOfAgile\NakaCMSBundle\Controller\Admin;

use App\Entity\BlockElementType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\CodeEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\Routing\Annotation\Route;

class BlockElementTypeCrudController extends AbstractCrudController
{
    /** @var AdminUrlGenerator */
    private $adminUrlGenerator;

    public function __construct(
        AdminUrlGenerator $adminUrlGenerator
    ) {
        $this->adminUrlGenerator = $adminUrlGenerator;
    }

    public static function getEntityFqcn(): string
    {
        return BlockElementType::class;
    }



    public function configureActions(Actions $actions): Actions
    {
        $blockElementTypeId = function (BlockElementType $blockElementType): array {
            return [
                'blockElementType' => $blockElementType->getId(),
            ];
        };
        $previewBlockElement = Action::new('preview', 'Preview Block Element', 'fa fa-eye')
            ->linkToRoute('preview_page_element', $blockElementTypeId)
            ->addCssClass('text-warning');
        return $actions
            ->add(Crud::PAGE_INDEX, $previewBlockElement);
    }

    public function configureFields(string $pageName): iterable
    {
        $id = IdField::new('id');
        // $name = TextField::new('name');
        $type = TextField::new(
            'type',
            'backend.form.blockElementType.type'
        );
        $htmlCode = CodeEditorField::new(
            'htmlCode',
            'backend.form.blockElementType.htmlCode'
        );
        $cssCode = CodeEditorField::new(
            'cssCode',
            'backend.form.blockElementType.cssCode'
        );
        $jsCode = CodeEditorField::new(
            'jsCode',
            'backend.form.blockElementType.jsCode'
        );


        if (Crud::PAGE_INDEX === $pageName) {
            return [$id, $type];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return [$id, $type, $htmlCode, $cssCode, $jsCode];
        } elseif (Crud::PAGE_NEW === $pageName) {
            return [$type, $htmlCode, $cssCode, $jsCode];
        } elseif (Crud::PAGE_EDIT === $pageName) {
            return [$type, $htmlCode, $cssCode, $jsCode];
        }
    }

    #[Route(path: '/preview/{blockElementType}', name: 'preview_page_element')]
    public function previewBlockElementType(
        BlockElementType $blockElementType
    ): \Symfony\Component\HttpFoundation\Response {

        $viewParams = [
            'blockElementType' => $blockElementType,
        ];

        return $this->render('@NakaCMS/naka/pages/show-block-element-type.html.twig', $viewParams);
    }

     

}
