<?php

namespace HouseOfAgile\NakaCMSBundle\Controller;

use HouseOfAgile\NakaCMSBundle\Component\OpeningHours\OpeningHoursManager;
use HouseOfAgile\NakaCMSBundle\Form\ContactType;
use HouseOfAgile\NakaCMSBundle\Service\Mailer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class ContactController extends AbstractController
{
    #[Route(path: '/{_locale<%app.supported_locales%>}/contact', name: 'app_contact')]
    public function contact(
        Request $request,
        EntityManagerInterface $em,
        OpeningHoursManager $openingHoursManager,
        Mailer $mailer
    ): Response {

        $form = $this->createForm(ContactType::class, null, []);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                // $data = $form->getData();
                $contact = $form->getData();
                $em->persist($contact);
                $em->flush();
                $this->addFlash(
                    'success',
                    'flash.contact.messageSent'
                );
                $mailer->sendContactNotificationEmail($contact);

                return $this->redirectToRoute('app_homepage');
            } else {
                $this->addFlash('error', sprintf(
                    'flash.contact.messageCannotBeSent'
                ));
            }
        }
        // get content from contact page if it exist
        $viewParams = [
            'contactForm' => $form->createView(),
            'contactBlockElements' => [],
            'openingHours' => $openingHoursManager->getOpeningHoursData(),
        ];
        return $this->render('@NakaCMS/contact.html.twig', $viewParams);
    }
}
