<?php
namespace HouseOfAgile\NakaCMSBundle\Controller;

use App\Entity\ContactMessage;
use App\Entity\ContactThread;
use App\Form\ContactThreadType;
use Doctrine\ORM\EntityManagerInterface;
use HouseOfAgile\NakaCMSBundle\Component\OpeningHours\OpeningHoursManager;
use HouseOfAgile\NakaCMSBundle\Service\Mailer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    #[Route(path: '/{_locale<%app.supported_locales%>}/contact', name: 'app_contact')]
    public function contact(
        Request $request,
        EntityManagerInterface $em,
        OpeningHoursManager $openingHoursManager,
        Mailer $mailer
    ): Response {
        // Create a new ContactThread (instead of old Contact entity)
        $form = $this->createForm(ContactThreadType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var ContactThread $contactThread */
            $contactThread = $form->getData();

            // Retrieve the user's message from the "mapped => false" field
            $messageText = $form->get('message')->getData();

            // Create the first ContactMessage
            $contactMessage = new ContactMessage();
            $contactMessage->setMessage($messageText);
            $contactMessage->setIsFromAdmin(false); // user-submitted message

            // Link them
            $contactThread->addMessage($contactMessage);

            // Persist the entire thread (this also persists the message due to cascade)
            $em->persist($contactThread);
            $em->flush();

            // Send your email notification
            // (Adjust your mailer to accept a ContactThread + ContactMessage)
            $mailer->sendContactNotificationEmail($contactThread, $contactMessage);

            // Add flash & redirect
            $this->addFlash('success', 'flash.contact.messageSent');
            return $this->redirectToRoute('app_homepage');
        }

        // If form not submitted or invalid, display again
        $viewParams = [
            'contactForm'          => $form->createView(),
            'contactBlockElements' => [],
            'openingHours'         => $openingHoursManager->getOpeningHoursData(),
        ];
        return $this->render('contact.html.twig', $viewParams);
    }
}
