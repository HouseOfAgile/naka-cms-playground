
To override a crud, you just need to setController on the menuItem in your dashboard.


```
<?php

namespace App\Controller\Admin;

use App\DBAL\Types\SomePageType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use HouseOfAgile\NakaCMSBundle\Controller\Admin\PageCrudController as BasePageCrudController;

class PageCrudController extends BasePageCrudController
{

    public function configureFields(string $pageName): iterable
    {
        $fieldsFromNaka = parent::configureFields($pageName);

        $somePageType = ChoiceField::new('somePageType')
            ->setChoices(SomePageType::getGuessOptions())
            ->setHelp('backend.form.page.somePageType.help')
            ->setFormTypeOption('required', false);


        if (Crud::PAGE_INDEX === $pageName) {
            return array_merge($fieldsFromNaka, [$somePageType]);
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return array_merge($fieldsFromNaka, [$somePageType]);
        } elseif (Crud::PAGE_NEW === $pageName) {
            return array_merge($fieldsFromNaka, [$somePageType]);
        } elseif (Crud::PAGE_EDIT === $pageName) {
            return array_merge($fieldsFromNaka, [$somePageType]);
        }
    }
}
```

