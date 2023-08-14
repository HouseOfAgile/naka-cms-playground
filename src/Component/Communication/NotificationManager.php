<?php

declare(strict_types=1);

namespace HouseOfAgile\NakaCMSBundle\Component\Communication;

use App\Entity\Newsletter;
use App\Entity\User;
use App\Repository\NewsletterRepository;
use App\Service\Mailer;
use Doctrine\ORM\EntityManagerInterface;
use HouseOfAgile\NakaCMSBundle\Repository\UserRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\Uid\Ulid;

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
        $this->mailer->sendNewMemberNotificationToOffice($user);

    }

}
