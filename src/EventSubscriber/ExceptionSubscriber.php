<?php

namespace HouseOfAgile\NakaCMSBundle\EventSubscriber;

use HouseOfAgile\NakaCMSBundle\Service\SlackNotificationService;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Event subscriber that sends error notifications to Slack
 * Captures 404, 500, and other 5xx errors
 */
class ExceptionSubscriber implements EventSubscriberInterface
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
            // Use high priority to catch exceptions early
            KernelEvents::EXCEPTION => ['onKernelException', 10],
        ];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        // Skip if Slack notifications are not enabled
        if (!$this->slackService->isEnabled() || !$this->slackService->hasErrorsWebhook()) {
            return;
        }

        $exception = $event->getThrowable();
        $request = $event->getRequest();

        // Determine status code
        $statusCode = $exception instanceof HttpExceptionInterface
            ? $exception->getStatusCode()
            : 500;

        // Only notify for specific error codes: 404, 500, and all 5xx
        if (!$this->shouldNotify($statusCode, $request)) {
            return;
        }

        // Build context information
        $context = $this->buildContext($request);

        // Send notification to Slack
        try {
            $this->slackService->notifyError(
                $statusCode,
                $request->getUri(),
                $request->headers->get('referer'),
                $exception,
                $context
            );
        } catch (\Throwable $e) {
            // Don't let notification failure break the application
            $this->logger->error('Failed to send Slack error notification', [
                'error' => $e->getMessage(),
                'original_exception' => get_class($exception),
            ]);
        }
    }

    /**
     * Determine if we should send notification for this status code
     */
    private function shouldNotify(int $statusCode, Request $request = null): bool
    {
        // Always notify for 500 and all 5xx errors (critical)
        if ($statusCode >= 500 && $statusCode < 600) {
            return true;
        }

        // For 404 errors, apply smarter filtering
        if ($statusCode === 404 && $request) {
            // Skip notification if URL is missing locale prefix (will be auto-redirected)
            if ($this->isMissingLocalePrefix($request->getPathInfo())) {
                return false;
            }

            // Skip crawler 404s to reduce noise (they'll retry after redirect)
            if ($this->isKnownCrawler($request->headers->get('User-Agent'))) {
                return false;
            }

            return true;
        }

        return false;
    }

    /**
     * Check if URL is missing locale prefix
     * These URLs will be automatically redirected by LocaleRedirectSubscriber
     */
    private function isMissingLocalePrefix(string $path): bool
    {
        $supportedLocales = ['en', 'fr', 'de', 'es', 'it', 'zh'];

        // Skip root URL
        if ($path === '/') {
            return false;
        }

        // Check if path starts with any supported locale
        foreach ($supportedLocales as $locale) {
            if (str_starts_with($path, '/' . $locale . '/')) {
                return false; // Has locale prefix
            }
        }

        // No locale prefix found
        return true;
    }

    /**
     * Detect known web crawlers/bots
     */
    private function isKnownCrawler(?string $userAgent): bool
    {
        if (!$userAgent) {
            return false;
        }

        $crawlerPatterns = [
            'bot',
            'crawler',
            'spider',
            'meta-externalagent', // Facebook crawler
            'AhrefsBot',
            'Googlebot',
            'Bingbot',
            'Slackbot',
            'LinkedInBot',
            'Twitterbot',
            'facebookexternalhit',
        ];

        $userAgentLower = strtolower($userAgent);
        foreach ($crawlerPatterns as $pattern) {
            if (str_contains($userAgentLower, strtolower($pattern))) {
                return true;
            }
        }

        return false;
    }

    /**
     * Build context array with request information
     */
    private function buildContext(Request $request): array
    {
        return [
            'environment' => $this->environment,
            'timestamp' => (new \DateTime())->format('Y-m-d H:i:s T'),
            'ip' => $request->getClientIp(),
            'user_agent' => $request->headers->get('User-Agent'),
            'method' => $request->getMethod(),
            'scheme' => $request->getScheme(),
            'host' => $request->getHost(),
        ];
    }
}
