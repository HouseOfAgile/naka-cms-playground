<?php

declare(strict_types=1);

namespace HouseOfAgile\NakaCMSBundle\Component\Communication;

use App\Service\Mailer;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class NotificationManager
{
    /** @var EntityManagerInterface */
    protected $entityManager;

    /** @var LoggerInterface */
    protected $logger;

    /** @var Mailer */
    protected $mailer;

    public function __construct(
        EntityManagerInterface $entityManager,
        LoggerInterface $bookingLogger,
        Mailer $mailer,
    ) {
        $this->entityManager = $entityManager;
        $this->logger = $bookingLogger;
        $this->mailer = $mailer;
    }

    public function notificationNewMemberVerified($user)
    {
        $this->mailer->sendWelcomeMessageToNewVerifiedMember($user);
        $this->mailer->sendNewMemberNotificationToOffice($user);

    }

}
