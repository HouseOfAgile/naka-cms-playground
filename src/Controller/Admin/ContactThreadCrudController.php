<?php
namespace HouseOfAgile\NakaCMSBundle\Controller\Admin;

use App\Entity\ContactThread;
use App\Entity\ContactMessage;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use HouseOfAgile\NakaCMSBundle\Config\ContactThreadStatus;
use HouseOfAgile\NakaCMSBundle\Controller\Admin\ContactMessageCrudController;
use Symfony\Component\HttpFoundation\RedirectResponse;

class ContactThreadCrudController extends AbstractCrudController
{
    private AdminUrlGenerator $adminUrlGenerator;
    private EntityManagerInterface $entityManager;

    public function __construct(AdminUrlGenerator $adminUrlGenerator, EntityManagerInterface $entityManager)
    {
        $this->adminUrlGenerator = $adminUrlGenerator;
        $this->entityManager     = $entityManager;
    }

    public static function getEntityFqcn(): string
    {
        return ContactThread::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        // 'Mark as Spam' action
        $markSpam = Action::new ('markSpam', 'Mark as Spam', 'fa fa-ban')
            ->linkToCrudAction('markSpam')
            ->setCssClass('btn btn-danger')
            ->displayIf(static function ($entity) {
                return $entity instanceof ContactThread
                && $entity->getStatus() !== ContactThreadStatus::SPAM;
            });

        // 'Answer' action
        $answer = Action::new ('answer', 'Answer', 'fa fa-reply')
            ->linkToCrudAction('answer')
            ->setCssClass('btn btn-success')
            ->displayIf(static function ($entity) {
                return $entity instanceof ContactThread
                && $entity->getStatus() !== ContactThreadStatus::ANSWERED
                && $entity->getStatus() !== ContactThreadStatus::SPAM;
            });

        return $actions
            // ->remove(Crud::PAGE_INDEX, Action::DETAIL)
            ->remove(Crud::PAGE_INDEX, Action::EDIT)
            ->remove(Crud::PAGE_INDEX, Action::DELETE)
            // ->add(Crud::PAGE_INDEX, $markSpam)
            // ->add(Crud::PAGE_INDEX, $answer)
            // ->add(Crud::PAGE_DETAIL, $markSpam)
            // ->add(Crud::PAGE_DETAIL, $answer)
			;
    }

    /**
     * Mark a thread as spam
     */
    public function markSpam(AdminContext $context): RedirectResponse
    {
        /** @var ContactThread $thread */
        $thread = $context->getEntity()->getInstance();
        if (! $thread instanceof ContactThread) {
            $this->addFlash('error', 'Unable to mark spam: entity not recognized.');
            return $this->redirectBackOrIndex($context->getReferrer());
        }

        $thread->setStatus(ContactThreadStatus::SPAM);
        $this->entityManager->flush();

        $this->addFlash('success', sprintf('Thread #%d marked as spam.', $thread->getId()));
        return $this->redirectBackOrIndex($context->getReferrer());
    }

    /**
     * Mark a thread as answered
     */
    public function answer(AdminContext $context): RedirectResponse
    {
        /** @var ContactThread $thread */
        $thread = $context->getEntity()->getInstance();
        if (! $thread instanceof ContactThread) {
            $this->addFlash('error', 'Unable to answer: entity not recognized.');
            return $this->redirectBackOrIndex($context->getReferrer());
        }

        $thread->setStatus(ContactThreadStatus::ANSWERED);
        $this->entityManager->flush();

        $this->addFlash('success', sprintf('Thread #%d marked as answered.', $thread->getId()));
        return $this->redirectBackOrIndex($context->getReferrer());
    }

    /**
     * Helper: redirects to referrer or index if referrer is null
     */
    private function redirectBackOrIndex(?string $referrer): RedirectResponse
    {
        if ($referrer) {
            return $this->redirect($referrer);
        }

        // fallback: go to index page
        $url = $this->adminUrlGenerator
            ->setController(self::class)
            ->setAction(Action::INDEX)
            ->generateUrl();

        return $this->redirect($url);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new ('id')->onlyOnIndex(),

            // subject
            TextField::new ('subject'),

            // status
            ChoiceField::new ('status')
                ->setChoices([
                    'New'      => ContactThreadStatus::NEW ,
                    'Answered' => ContactThreadStatus::ANSWERED,
                    'Spam'     => ContactThreadStatus::SPAM,
                ])
                ->hideOnDetail()->hideOnForm()->hideOnIndex(),

            // show name & email
            TextField::new ('name'),
            TextField::new ('email'),

            TextField::new ('firstMessageText', 'Message (truncated to 50 chars)')
                ->onlyOnIndex()
                ->formatValue(function (?string $value, ?ContactThread $thread) {
                    if (! $thread) {
                        return '';
                    }
                    // Grab the first message text, truncated to 50 chars
                    return $thread->getFirstMessageText(50);
                }),

            // allow sub-form for messages in create/edit
            CollectionField::new ('messages')
                ->onlyOnForms()
                ->useEntryCrudForm(ContactMessageCrudController::class),
        ];
    }
}
