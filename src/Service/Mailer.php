<?php

namespace HouseOfAgile\NakaCMSBundle\Service;

use HouseOfAgile\NakaCMSBundle\Entity\BaseUser;
use HouseOfAgile\NakaCMSBundle\Entity\Contact;
use HouseOfAgile\NakaCMSBundle\Entity\User;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\NamedAddress;
use Twig\Environment;

class Mailer
{
    private $mailer;
    private $twig;

    /** @var LoggerInterface */
    protected $logger;
    protected $applicationSenderEmail;
    protected $applicationContactEmail;

    public function __construct(
        MailerInterface $mailer,
        Environment $twig,
        LoggerInterface $generalLogger,
        $applicationSenderEmail,
        $applicationContactEmail
    ) {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->logger = $generalLogger;
        $this->applicationSenderEmail = $applicationSenderEmail;
        $this->applicationContactEmail = $applicationContactEmail;
    }

    public function sendWelcomeMessageToOrganizer(BaseUser $user)
    {
        $email = (new TemplatedEmail())
            // ->from(new NamedAddress('alienmailcarrier@example.com', 'The Space Bar'))
            ->from($this->applicationSenderEmail)
            ->to($user->getEmail())
            // ->to(new NamedAddress($user->getEmail(), $user->getFirstName()))
            ->subject('Welcome to the Space Bar!')
            ->htmlTemplate('email/welcome-organizer.html.twig')
            ->context([
                // You can pass whatever data you want
                'user' => $user,
            ]);

        $this->mailer->send($email);
        $this->logger->info(sprintf(
            'Welcome email has been send to organizer %s with email %s',
            $user,
            $user->getEmail()
        ));
    }


    public function sendContactNotificationEmail(Contact $contact)
    {
        $email = (new TemplatedEmail())
            // ->from(new NamedAddress('alienmailcarrier@example.com', 'The Space Bar'))
            ->from($this->applicationSenderEmail)
            ->to($this->applicationContactEmail)
            // ->to(new NamedAddress($user->getEmail(), $user->getFirstName()))
            ->subject('New contact Message!')
            ->htmlTemplate('email/new-contact-message.html.twig')
            ->context([
                // You can pass whatever data you want
                'contact' => $contact,
            ]);

        $this->mailer->send($email);
        $this->logger->info(sprintf(
            'New contact Message from email %s',
            $contact->getEmail()
        ));
    }
}
