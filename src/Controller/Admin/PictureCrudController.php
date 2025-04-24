<?php

namespace HouseOfAgile\NakaCMSBundle\Controller\Admin;

use App\Entity\Picture;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use Vich\UploaderBundle\Form\Type\VichImageType;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use HouseOfAgile\NakaCMSBundle\Admin\Field\TranslationField;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class PictureCrudController extends AbstractCrudController
{
	public static function getEntityFqcn(): string
	{
		return Picture::class;
	}

	public function configureFilters(Filters $filters): Filters
	{
		return $filters
			->add('name')
			->add('createdAt');
	}

	public function configureFields(string $pageName): iterable
	{
		$id = IdField::new('id');

		$imagePreview = ImageField::new('image.name')
			->setTemplatePath('@NakaCMS/admin/fields/vich_image.html.twig');

		$imageFile = Field::new('imageFile')->setFormType(VichImageType::class);

		$imageName = TextField::new('image.name');

		$fieldsConfig = [
			'altText' => [
				'field_type' => TextareaType::class,
				'required' => true,
				'label' => 'Alt Text for picture (SEO)',
			],
		];

		$name = TextField::new('name')
			->setLabel('Name')
			->setHelp('Internal name to be used in Menus and elsewhere');

		$translations = TranslationField::new('translations', 'Translations', $fieldsConfig)
			->setRequired(false)
			->hideOnIndex()
			->setHelp('backend.form.picture.translations.help');

		if (Crud::PAGE_INDEX === $pageName) {
			return [$id, $name, $imageName, $imagePreview];
		} elseif (Crud::PAGE_DETAIL === $pageName) {
			return [$id, $name, $imagePreview];
		} elseif (Crud::PAGE_NEW === $pageName) {
			return [$name, $imageFile, $translations];
		} elseif (Crud::PAGE_EDIT === $pageName) {
			return [$name, $imageFile, $translations];
		}
	}
}
