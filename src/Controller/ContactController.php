<?php

namespace HouseOfAgile\NakaCMSBundle\Controller;

use HouseOfAgile\NakaCMSBundle\Form\ContactType;
use HouseOfAgile\NakaCMSBundle\Service\Mailer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    /**
     * @Route("/contact", name="app_contact")
     */
    public function contact(
        Request $request,
        Mailer $mailer
    ): Response {

        $form = $this->createForm(ContactType::class, null, []);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                // $data = $form->getData();
                $contact = $form->getData();
                $em = $this->getDoctrine()->getManager();
                $em->persist($contact);
                $em->flush();
                $this->addFlash(
                    'success',
                    'app.flash.contact.message-sent'
                );
                $mailer->sendContactNotificationEmail($contact);

                return $this->redirectToRoute('app_homepage');
            } else {
                $this->addFlash('error', sprintf(
                    'app.flash.contact.message-cannot-be-sent'
                ));
            }
        }
        // get content from contact page if it exist
        $viewParams = [
            'form' => $form->createView(),
            'contactBlockElements' => [],
        ];
        return $this->render('naka/contact.html.twig', $viewParams);
    }
}
