<?php

namespace HouseOfAgile\NakaCMSBundle\Service;

use HouseOfAgile\NakaCMSBundle\Entity\BaseUser;
use HouseOfAgile\NakaCMSBundle\Entity\Contact;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\NamedAddress;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class Mailer
{
    protected $mailer;
    protected $twig;

    /** @var LoggerInterface */
    protected $logger;
    protected $applicationName;
    protected $applicationSenderEmail;
    protected $applicationContactEmail;

    /** @var TranslatorInterface */
    protected $translator;

    /** @var UrlGeneratorInterface */
    protected $router;

    public function __construct(
        MailerInterface $mailer,
        Environment $twig,
        LoggerInterface $generalLogger,
        $applicationName,
        $applicationSenderEmail,
        $applicationContactEmail,
        TranslatorInterface $translator,
        UrlGeneratorInterface $router,

    ) {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->logger = $generalLogger;
        $this->applicationName = $applicationName;
        $this->applicationSenderEmail = $applicationSenderEmail;
        $this->applicationContactEmail = $applicationContactEmail;
        $this->translator = $translator;
        $this->router = $router;
    }

    public function sendWelcomeMessageToEditor(BaseUser $user)
    {
        $email = (new TemplatedEmail())
            // ->from(new NamedAddress('alienmailcarrier@example.com', 'The Space Bar'))
            ->from($this->applicationSenderEmail)
            ->to($user->getEmail())
            // ->to(new NamedAddress($user->getEmail(), $user->getFirstName()))
            ->subject(sprintf('Welcome to %s!', $this->applicationName))
            ->htmlTemplate('@NakaCMS/email/welcome-editor.html.twig')
            ->context([
                // You can pass whatever data you want
                'user' => $user,
            ]);

        $this->mailer->send($email);
        $this->logger->info(sprintf(
            'Welcome email has been send to editor %s with email %s',
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
            ->htmlTemplate('@NakaCMS/email/new-contact-message.html.twig')
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
