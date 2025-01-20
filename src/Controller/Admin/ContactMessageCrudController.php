<?php

namespace HouseOfAgile\NakaCMSBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use HouseOfAgile\NakaCMSBundle\Entity\ContactMessage;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;

class ContactMessageCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ContactMessage::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            TextareaField::new('message'),
            BooleanField::new('isFromAdmin', 'From Admin?'),
            DateTimeField::new('createdAt')
                ->onlyOnDetail(),
            // Usually you want to hide the thread field if the message is 
            // always created in the context of a thread:
            AssociationField::new('thread')
                ->hideOnForm(),
        ];
    }
}
