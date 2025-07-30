<?php

namespace HouseOfAgile\NakaCMSBundle\Controller\Admin;

use App\Component\Communication\NotificationManager;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\LocaleField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use HouseOfAgile\NakaCMSBundle\Entity\BaseUser;
use HouseOfAgile\NakaCMSBundle\Form\Backend\BackendAddressType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class BaseUserCrudController extends AbstractCrudController implements EventSubscriberInterface
{

    /** @var UserPasswordHasherInterface */
    private UserPasswordHasherInterface $passwordHasher;

    /** @var NotificationManager */
    private $notificationManager;

    protected $applicationName;

    private array $allLocales;

    public function __construct(
        UserPasswordHasherInterface $passwordHasher,
        NotificationManager $notificationManager,
        $applicationName,
        array $allLocales
    ) {
        $this->passwordHasher = $passwordHasher;
        $this->notificationManager = $notificationManager;
        $this->applicationName = $applicationName;
        $this->allLocales = $allLocales;
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
        $id = IntegerField::new('id', 'ID')->onlyOnIndex();

        $firstName = TextField::new('firstName', 'backend.form.user.firstName')
            ->setHelp('backend.form.user.firstName.help')
            ->setColumns(6);

        $lastName = TextField::new('lastName', 'backend.form.user.lastName')
            ->setHelp('backend.form.user.lastName.help')
            ->setColumns(6);

        $userDetails = TextField::new('userDetails', 'backend.form.user.userDetails')
            ->setHelp('backend.form.user.lastName.help')
            ->setColumns(6)
            ->setTemplatePath('@NakaCMS/backend/fields/user/user_details.html.twig');

        $address = Field::new('addressFields')
            ->setFormType(BackendAddressType::class)
            ->setFormTypeOptions([
                'label' => false,
            ]);

        $email = TextField::new('email', 'backend.form.user.email')
            ->setHelp('backend.form.user.email.help');

        $birthDate = DateField::new('birthDate', 'backend.form.user.birthDate')
            ->setHelp('backend.form.user.birthDate.help');
        $preferredLocale = LocaleField::new('preferredLocale', 'backend.form.user.preferredLocale')
            ->includeOnly($this->allLocales)
            ->setHelp('backend.form.user.preferredLocale.help');

        $isVerified = BooleanField::new('isVerified')
            ->setHelp('backend.form.user.isVerified.help')
            ->setColumns(6);

        $roles = ChoiceField::new('roles', 'backend.form.user.roles')
            ->setChoices([
                'backend.form.user.role.user' => 'ROLE_USER',
                'backend.form.user.role.admin' => 'ROLE_ADMIN',
            ])->allowMultipleChoices()
            ->setHelp('backend.form.user.roles.help');

        $password = Field::new('plainPassword', 'backend.form.user.password')
            ->onlyOnForms()
            ->setFormType(RepeatedType::class)
            ->setFormTypeOptions([
                'type' => PasswordType::class,
                'first_options' => ['label' => 'backend.form.user.password.new'],
                'second_options' => ['label' => 'backend.form.user.password.repeat'],
            ])
            ->setFormTypeOption('required', false);

        $tabUserfields = [
            FormField::addTab('backend.form.user.details'),

            FormField::addColumn(8),
            FormField::addFieldset('backend.form.user.userDetails')
                ->setIcon('fa fa-info'),
            $id,
            $firstName,
            $lastName,
            $email,
            $birthDate,
            FormField::addColumn(4),

            FormField::addFieldset('backend.form.user.userPreferences')
                ->setIcon('fa fa-heart'),
            $preferredLocale,
            FormField::addFieldset('backend.form.user.userAddress')
                ->setIcon('fa fa-map-marker'),
            $address,
        ];

        $tabUserAdminFields = [
            FormField::addTab('backend.form.user.adminConfiguration')->setIcon('wheel'),
            FormField::addColumn(6),
            FormField::addFieldset('backend.form.user.userAuthentication'),
            $password,
            $roles,
            $isVerified,
        ];

        if (!$this->isGranted('ROLE_ADMIN')) {
            $firstName->setDisabled(true);
            $lastName->setDisabled(true);
            $email->setDisabled(true);
            $birthDate->setDisabled(true);
            $preferredLocale->setDisabled(true);
            $roles->setDisabled(true);
            $password->setDisabled(true);
            $isVerified->setDisabled(true);
        }

        if (!$this->isGranted('ROLE_SUPER_ADMIN')) {
            $birthDate->setDisabled(true);
        }

        if ($this->isGranted('ROLE_ADMIN')) {
            $mainUserfields = array_merge($tabUserfields, $tabUserAdminFields);
        } else {
            $mainUserfields = array_merge($tabUserfields);
        }

        if (Crud::PAGE_INDEX === $pageName) {
            return [$id, $lastName, $firstName, $email, $userDetails, $isVerified];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return array_merge($mainUserfields);
        } elseif (Crud::PAGE_NEW === $pageName) {
            return array_merge($mainUserfields);
        } elseif (Crud::PAGE_EDIT === $pageName) {
            return array_merge($mainUserfields);
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            BeforeEntityPersistedEvent::class => 'encodePassword',
            AfterEntityPersistedEvent::class => 'welcomeNewUser',
            BeforeEntityUpdatedEvent::class => 'encodePassword',
        ];
    }

    /** @internal */
    public function encodePassword($event)
    {
        $user = $event->getEntityInstance();

        if ($user instanceof BaseUser && !empty($user->getPlainPassword())) {
            // Only hash the password if it's not empty
            $hashedPassword = $this->passwordHasher->hashPassword(
                $user,
                $user->getPlainPassword()
            );
            $user->setPassword($hashedPassword);
            $user->setPlainPassword(null); // Clear the plain password after setting
        }
    }

    public function welcomeNewUser($event)
    {
        $user = $event->getEntityInstance();
        if ($user instanceof BaseUser) {
            if (!in_array('ROLE_ADMIN', $user->getRoles(), true)) {
                // we shoudld send specific mails to new users based on their role
                $this->notificationManager->notificationNewMemberVerified($user);
            }
        }
    }
}
