<?php

declare(strict_types=1);

namespace HouseOfAgile\NakaCMSBundle\Component\Communication;

use App\Service\Mailer;
use Doctrine\ORM\EntityManagerInterface;
use HouseOfAgile\NakaCMSBundle\Service\SlackNotificationService;
use Psr\Log\LoggerInterface;

class NotificationManager
{
    /** @var EntityManagerInterface */
    protected $entityManager;

    /** @var LoggerInterface */
    protected $logger;

    /** @var Mailer */
    protected $mailer;

    /** @var SlackNotificationService */
    protected $slackService;

    public function __construct(
        EntityManagerInterface $entityManager,
        LoggerInterface $bookingLogger,
        Mailer $mailer,
        SlackNotificationService $slackService
    ) {
        $this->entityManager = $entityManager;
        $this->logger = $bookingLogger;
        $this->mailer = $mailer;
        $this->slackService = $slackService;
    }

    public function notificationNewMemberVerified($user)
    {
        $this->mailer->sendWelcomeMessageToNewVerifiedMember($user);
        $this->mailer->sendNewMemberNotificationToOffice($user);
    }

    /**
     * Get the Slack notification service
     */
    public function getSlackService(): SlackNotificationService
    {
        return $this->slackService;
    }
}
