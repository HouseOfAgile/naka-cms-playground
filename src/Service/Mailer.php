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
use Symfony\Component\Mime\Address;

class Mailer
{
    protected $mailer;
    protected $twig;

    /** @var LoggerInterface */
    protected $logger;
    protected $applicationName;
    protected $applicationSenderEmail;
    protected $applicationSenderName;
    protected $applicationContactEmail;
    protected $applicationContactName;
    protected $senderAddress;
    protected $contactAddress;

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
        $applicationSenderName,
        $applicationContactEmail,
        $applicationContactName,
        TranslatorInterface $translator,
        UrlGeneratorInterface $router,

    ) {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->logger = $generalLogger;
        $this->applicationName = $applicationName;
        $this->applicationSenderEmail = $applicationSenderEmail;
        $this->applicationSenderName = $applicationSenderName;
        $this->applicationContactEmail = $applicationContactEmail;
        $this->applicationContactName = $applicationContactName;
        $this->senderAddress = new Address($applicationSenderEmail, $applicationSenderName);
        $this->contactAddress = new Address($applicationContactEmail, $applicationContactName);
        $this->translator = $translator;
        $this->router = $router;
    }

    public function sendWelcomeMessageToEditor(BaseUser $user)
    {
        $email = (new TemplatedEmail())
            // ->from(new NamedAddress('alienmailcarrier@example.com', 'The Space Bar'))
            ->from($this->senderAddress)
            ->to($user->getEmail())
            // ->to(new NamedAddress($user->getEmail(), $user->getFirstName()))
            ->subject($this->translator->trans('email.welcomeMessageToEditor.subject', ['%applicationName%' => $this->applicationName]))
            ->htmlTemplate('@NakaCMS/email/welcome_editor.html.twig')
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
            ->from($this->senderAddress)
            ->to($this->contactAddress)
            // ->to(new NamedAddress($user->getEmail(), $user->getFirstName()))
            ->subject($this->translator->trans('email.newContactMessage.subject', ['%applicationName%' => $this->applicationName]))
            ->htmlTemplate('@NakaCMS/email/new_contact_message.html.twig')
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

    public function sendResetPasswordEmail($email, $resetToken)
    {
        $templatedEmail = (new TemplatedEmail())
            ->from($this->senderAddress)
            ->to($email)
            ->subject($this->translator->trans('email.resetPassword.subject', ['%applicationName%' => $this->applicationName]))
            ->htmlTemplate('@NakaCMS/email/reset_password_email.html.twig')
            ->context([
                'resetToken' => $resetToken,
            ]);
        $this->mailer->send($templatedEmail);
        $this->logger->info(sprintf(
            'New Reset email mail sent to email %s',
            $email
        ));
    }
}
