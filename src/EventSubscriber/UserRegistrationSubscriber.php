<?php

namespace HouseOfAgile\NakaCMSBundle\EventSubscriber;

use HouseOfAgile\NakaCMSBundle\Event\UserRegistrationEvent;
use HouseOfAgile\NakaCMSBundle\Service\SlackNotificationService;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Event subscriber that sends notifications to Slack when users register
 */
class UserRegistrationSubscriber implements EventSubscriberInterface
{
    private SlackNotificationService $slackService;
    private LoggerInterface $logger;
    private string $environment;

    public function __construct(
        SlackNotificationService $slackService,
        LoggerInterface $generalLogger,
        string $appEnv
    ) {
        $this->slackService = $slackService;
        $this->logger = $generalLogger;
        $this->environment = $appEnv;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            UserRegistrationEvent::NAME => 'onUserRegistration',
        ];
    }

    public function onUserRegistration(UserRegistrationEvent $event): void
    {
        // Skip if Slack notifications are not enabled
        if (!$this->slackService->isEnabled() || !$this->slackService->hasRegistrationsWebhook()) {
            return;
        }

        $user = $event->getUser();

        // Build context from event + environment info
        $context = array_merge(
            $event->getContext(),
            [
                'environment' => $this->environment,
                'timestamp' => (new \DateTime())->format('Y-m-d H:i:s T'),
            ]
        );

        // Send notification to Slack
        try {
            $this->slackService->notifyRegistration(
                $user->getUsername() ?? 'Unknown',
                $user->getEmail() ?? 'no-email@example.com',
                $context
            );

            $this->logger->info('User registration notification sent to Slack', [
                'user_id' => $user->getId(),
                'username' => $user->getUsername(),
            ]);
        } catch (\Throwable $e) {
            // Don't let notification failure break the registration process
            $this->logger->error('Failed to send Slack registration notification', [
                'error' => $e->getMessage(),
                'user_id' => $user->getId(),
                'username' => $user->getUsername(),
            ]);
        }
    }
}
