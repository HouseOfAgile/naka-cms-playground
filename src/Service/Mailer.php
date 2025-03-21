<?php
namespace HouseOfAgile\NakaCMSBundle\Service;

use App\Entity\ContactMessage;
use App\Entity\ContactThread;
use HouseOfAgile\NakaCMSBundle\Entity\BaseUser;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
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
    protected $applicationDoNotReplyEmail;
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
        $applicationDoNotReplyEmail,
        $applicationSenderName,
        $applicationContactEmail,
        $applicationContactName,
        TranslatorInterface $translator,
        UrlGeneratorInterface $router,

    ) {
        $this->mailer                     = $mailer;
        $this->twig                       = $twig;
        $this->logger                     = $generalLogger;
        $this->applicationName            = $applicationName;
        $this->applicationSenderEmail     = $applicationSenderEmail;
        $this->applicationDoNotReplyEmail = $applicationDoNotReplyEmail;
        $this->applicationSenderName      = $applicationSenderName;
        $this->applicationContactEmail    = $applicationContactEmail;
        $this->applicationContactName     = $applicationContactName;
        $this->senderAddress              = new Address($applicationSenderEmail, $applicationSenderName);
        $this->contactAddress             = new Address($applicationContactEmail, $applicationContactName);
        $this->translator                 = $translator;
        $this->router                     = $router;
    }

    public function sendMessageToAddress($fromAddress, $toAddress, $subject, $templateName, $context, $locale = 'en', $addDoNotReply = true)
    {
        $context        = array_merge($context, ['_locale' => $locale]);
        $templatedEmail = (new TemplatedEmail())
            ->from($fromAddress)
            ->to($toAddress)
            ->subject($subject)
            ->htmlTemplate($templateName)
            ->locale($locale)
            ->context($context);

        if ($addDoNotReply) {
            $templatedEmail->replyTo($this->applicationDoNotReplyEmail);
        }
        $this->mailer->send($templatedEmail);
        $this->logger->info(sprintf(
            'New mail sent to %s about %s',
            $toAddress instanceof Address ? $toAddress->toString() : $toAddress,
            $subject,
        ));
    }

    public function sendWelcomeMessageToNewVerifiedMember(BaseUser $user)
    {
        $this->sendMessageToAddress(
            $this->senderAddress,
            $user->getEmail(),
            $this->translator->trans('email.welcomeMessageToNewVerifiedMember.subject', ['%applicationName%' => $this->applicationName]),
            '@NakaCMS/email/welcome_new_verified_member.html.twig',
            [
                'user' => $user,
            ],
            $user->getPreferredLocale()
        );
        $this->logger->info(sprintf(
            'Welcome email has been send to editor %s with email %s',
            $user,
            $user->getEmail()
        ));
    }

    public function sendVerifyMessageToNewMember(BaseUser $user, $verifyLink = 'tbd')
    {
        $this->sendMessageToAddress(
            $this->senderAddress,
            $user->getEmail(),
            $this->translator->trans('email.welcomeUserVerifyEmail.title', ['%applicationName%' => $this->applicationName]),
            '@NakaCMS/email/welcome_new_member_verify.html.twig',
            [
                'user'       => $user,
                'verifyLink' => $verifyLink,
            ]
        );
        $this->logger->info(sprintf(
            'Welcome email has been send to new member %s with email %s',
            $user,
            $user->getEmail()
        ));
    }

    public function sendContactNotificationEmail(ContactThread $thread, ContactMessage $message): void
    {
        $this->sendMessageToAddress(
            $this->senderAddress,
            $this->contactAddress,
            $this->translator->trans('email.office.newContactMessage.subject', ['%applicationName%' => $this->applicationName]),
            '@NakaCMS/email/new_contact_message.html.twig',
            [
                'thread' => $thread,
                'message' => $message,
            ]
        );
        $this->logger->info(sprintf(
            'New contact Message from email %s',
            $thread->getEmail()
        ));
    }

    public function sendResetPasswordEmail($email, $resetToken)
    {
        $this->sendMessageToAddress(
            $this->senderAddress,
            $email,
            $this->translator->trans('email.resetPassword.subject', ['%applicationName%' => $this->applicationName]),
            '@NakaCMS/email/reset_password_email.html.twig',
            [
                'resetToken' => $resetToken,
            ]
        );
        $this->logger->info(sprintf(
            'New Reset email mail sent to email %s',
            $email
        ));
    }

    public function sendNewMemberNotificationToOffice(BaseUser $user)
    {
        $this->sendMessageToAddress(
            $this->senderAddress,
            $this->contactAddress,
            $this->translator->trans('email.office.newMember.subject', ['%applicationName%' => $this->applicationName]),
            '@NakaCMS/email/office_new_member_email.html.twig',
            [
                'user' => $user,
            ]
        );
        $this->logger->info(sprintf(
            'New member email mail sent to office',
        ));
    }
}
