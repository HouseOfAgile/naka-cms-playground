<?php

namespace HouseOfAgile\NakaCMSBundle\Controller\Admin;

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
use HouseOfAgile\NakaCMSBundle\Entity\ContactThread;
use Symfony\Component\HttpFoundation\RedirectResponse;

class ContactThreadCrudController extends AbstractCrudController
{
    protected AdminUrlGenerator $adminUrlGenerator;
    protected EntityManagerInterface $entityManager;

    public function __construct(AdminUrlGenerator $adminUrlGenerator, EntityManagerInterface $entityManager)
    {
        $this->adminUrlGenerator = $adminUrlGenerator;
        $this->entityManager     = $entityManager;
    }

    public static function getEntityFqcn(): string
    {
        return ContactThread::class;
    }

    /**
     * Add custom actions for marking spam and answering a thread.
     */
    public function configureActions(Actions $actions): Actions
    {
        // Custom 'Mark as Spam' action
        $markSpam = Action::new('markSpam', 'Mark as Spam', 'fa fa-ban')
            ->linkToCrudAction('markSpam')
            ->setCssClass('btn btn-danger')
            ->displayIf(static function ($entity) {
                return $entity instanceof ContactThread
                    && $entity->getStatus() !== ContactThreadStatus::SPAM;
            });

        // Custom 'Answer' action
        $answer = Action::new('answer', 'Answer', 'fa fa-reply')
            ->linkToCrudAction('answer')
            ->setCssClass('btn btn-success')
            ->displayIf(static function ($entity) {
                return $entity instanceof ContactThread
                    && $entity->getStatus() !== ContactThreadStatus::ANSWERED;
            });

        return $actions
            ->add(Crud::PAGE_INDEX, $markSpam)
            ->add(Crud::PAGE_INDEX, $answer)
            ->add(Crud::PAGE_DETAIL, $markSpam)
            ->add(Crud::PAGE_DETAIL, $answer);
    }

    /**
     * Controller method for marking a thread as spam
     */
    public function markSpam(AdminContext $context): RedirectResponse
    {
        /** @var ContactThread $thread */
        $thread = $context->getEntity()->getInstance();
        if (!$thread instanceof ContactThread) {
            $this->addFlash('error', 'Unable to mark spam: entity not recognized.');
            return $this->redirectBackOrIndex($context->getReferrer());
        }

        $thread->setStatus(ContactThreadStatus::SPAM);
        $this->entityManager->flush();

        $this->addFlash('success', sprintf('Thread #%d marked as spam.', $thread->getId()));
        return $this->redirectBackOrIndex($context->getReferrer());
    }

    /**
     * Controller method for answering a thread
     */
    public function answer(AdminContext $context): RedirectResponse
    {
        /** @var ContactThread $thread */
        $thread = $context->getEntity()->getInstance();
        if (!$thread instanceof ContactThread) {
            $this->addFlash('error', 'Unable to answer: entity not recognized.');
            return $this->redirectBackOrIndex($context->getReferrer());
        }

        // Mark the thread as answered
        $thread->setStatus(ContactThreadStatus::ANSWERED);
        $this->entityManager->flush();

        $this->addFlash('success', sprintf('Thread #%d marked as answered.', $thread->getId()));
        return $this->redirectBackOrIndex($context->getReferrer());
    }

    /**
     * Helper: redirects to the referrer if not null, otherwise fallback to index.
     */
    private function redirectBackOrIndex(?string $referrer): RedirectResponse
    {
        if ($referrer) {
            return $this->redirect($referrer);
        }

        // fallback: go to the index page
        $url = $this->adminUrlGenerator
            ->setController(self::class)
            ->setAction(Action::INDEX)
            ->generateUrl();

        return $this->redirect($url);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')
                ->onlyOnIndex(),

            TextField::new('subject'),

            // We can manually set enum choices here for clarity:
            ChoiceField::new('status')
                ->setChoices([
                    'New' => ContactThreadStatus::NEW,
                    'Answered' => ContactThreadStatus::ANSWERED,
                    'Spam' => ContactThreadStatus::SPAM,
                ]),

            TextField::new('name')
                ->hideOnIndex(),

            TextField::new('email')
                ->hideOnIndex(),

            // Show messages (sub-form) on "edit" or "new" forms
            // so admin can create replies if needed
            CollectionField::new('messages')
                ->onlyOnForms()
                ->useEntryCrudForm(ContactMessageCrudController::class),

            // show the first message in the detail page
            TextField::new('firstMessage', 'Initial Message')
                ->onlyOnDetail()
                ->formatValue(function($value, $entity) {
                    if (!$entity instanceof ContactThread) {
                        return '';
                    }

                    // get the first message from $entity->getMessages()
                    $messages = $entity->getMessages();
                    if ($messages->count() === 0) {
                        return '';
                    }

                    // The "first" method on Doctrine's collection
                    // returns the first element in iteration order
                    $firstMessage = $messages->first()->getMessage();

                    return $firstMessage;
                }),
        ];
    }
}
