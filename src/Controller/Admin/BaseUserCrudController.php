<?php

namespace HouseOfAgile\NakaCMSBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use HouseOfAgile\NakaCMSBundle\Entity\BaseUser;
use HouseOfAgile\NakaCMSBundle\Service\Mailer;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class BaseUserCrudController extends AbstractCrudController implements EventSubscriberInterface
{

    /** @var UserPasswordEncoderInterface */
    private $passwordEncoder;

    /** @var Mailer */
    private $mailer;
    protected $applicationName;

    public function __construct(
        UserPasswordEncoderInterface $passwordEncoder,
        Mailer $mailer,
        $applicationName
    ) {
        $this->passwordEncoder = $passwordEncoder;
        $this->mailer = $mailer;
        $this->applicationName = $applicationName;
    }

    public static function getEntityFqcn(): string
    {
        return BaseUser::class;
    }


    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('User')
            ->setEntityLabelInPlural('Users')
            ->setSearchFields(['firstName', 'email', 'roles', 'id'])
            ->setPaginatorPageSize(30);
    }

    public function configureActions(Actions $actions): Actions
    {
        $actions = parent::configureActions($actions);
        return $actions
            // ->add(Crud::PAGE_INDEX, Action::NEW)
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        $panel = FormField::addTab('User Details');
        $panel3 = FormField::addTab('User Detailss');
        
        $firstName = TextField::new('firstName')
            ->setHelp('backend.form.user.firstName.help');

        $email = TextField::new('email')->setHelp('backend.form.user.email.help');

        // @todo Move roles definitions to some configuration file
        $rolesSuperAdmin = ChoiceField::new('roles')
            ->setChoices([
                'Content Editor' => 'ROLE_EDITOR',
                'Administrator' => 'ROLE_ADMIN',
                'Super Administrator' => 'ROLE_SUPER_ADMIN',
            ])
            ->allowMultipleChoices()
            ->setPermission('ROLE_SUPER_ADMIN');

        $roles = ChoiceField::new('roles')->setChoices([
            'Content Editor' => 'ROLE_EDITOR',
            'Administrator' => 'ROLE_ADMIN',
        ])->allowMultipleChoices()
            ->setHelp('backend.form.user.roles.help');



        $salt = TextField::new('salt');
        $password = Field::new('plainPassword', 'New password')->onlyOnForms()
            ->setFormType(RepeatedType::class)
            ->setFormTypeOptions([
                'type' => PasswordType::class,
                'first_options' => ['label' => 'New password'],
                'second_options' => ['label' => 'Repeat password'],
            ])
            ->setFormTypeOption('required', false);
        $id = IntegerField::new('id', 'ID');

        if (Crud::PAGE_INDEX === $pageName) {
            if ($this->isGranted('ROLE_SUPER_ADMIN')) {
                return [$firstName, $email, $rolesSuperAdmin];
            } else {
                return [$firstName, $email, $roles];
            }
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return [$firstName, $email, $salt, $password, $roles, $id];
        } elseif (Crud::PAGE_NEW === $pageName) {
            return [$firstName, $password, $email, $roles];
        } elseif (Crud::PAGE_EDIT === $pageName) {
            return [$panel, $firstName, $password,$panel3,  $email, $roles, $rolesSuperAdmin];
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            BeforeEntityPersistedEvent::class => 'encodePassword',
            AfterEntityPersistedEvent::class => 'welcomeNewEditor',
            BeforeEntityUpdatedEvent::class => 'encodePassword',
        ];
    }

    /** @internal */
    public function encodePassword($event)
    {
        $user = $event->getEntityInstance();
        if ($user instanceof BaseUser && $user->getPlainPassword()) {
            $user->setPassword($this->passwordEncoder->encodePassword($user, $user->getPlainPassword()));
            $user->setPlainPassword(null);
        }
    }


    public function welcomeNewEditor($event)
    {
        $user = $event->getEntityInstance();
        if ($user instanceof BaseUser) {
            if (in_array('ROLE_EDITOR', $user->getRoles(), true)) {
                $this->mailer->sendWelcomeMessageToEditor($user);
            }
        }
    }
}
