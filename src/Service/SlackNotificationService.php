<?php

namespace HouseOfAgile\NakaCMSBundle\Service;

use Psr\Log\LoggerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Service for sending notifications to Slack via webhooks
 * Supports rate limiting to prevent spam during incidents
 */
class SlackNotificationService
{
    private HttpClientInterface $httpClient;
    private LoggerInterface $logger;
    private CacheInterface $cache;
    private string $errorsWebhookUrl;
    private string $registrationsWebhookUrl;
    private bool $enabled;
    private int $rateLimitSeconds;

    public function __construct(
        HttpClientInterface $httpClient,
        LoggerInterface $generalLogger,
        CacheInterface $cache,
        string $slackWebhookErrorsUrl,
        string $slackWebhookRegistrationsUrl,
        bool $slackNotificationsEnabled,
        int $slackErrorRateLimitSeconds
    ) {
        $this->httpClient = $httpClient;
        $this->logger = $generalLogger;
        $this->cache = $cache;
        $this->errorsWebhookUrl = $slackWebhookErrorsUrl;
        $this->registrationsWebhookUrl = $slackWebhookRegistrationsUrl;
        $this->enabled = $slackNotificationsEnabled;
        $this->rateLimitSeconds = $slackErrorRateLimitSeconds;
    }

    /**
     * Send error notification to Slack errors channel
     *
     * @param int $statusCode HTTP status code (404, 500, etc.)
     * @param string $url The URL that generated the error
     * @param string|null $referrer The referrer URL
     * @param \Throwable|null $exception The exception object
     * @param array $context Additional context (user agent, IP, etc.)
     */
    public function notifyError(
        int $statusCode,
        string $url,
        ?string $referrer = null,
        ?\Throwable $exception = null,
        array $context = []
    ): void {
        if (!$this->enabled || empty($this->errorsWebhookUrl)) {
            $this->logger->debug('Slack error notification skipped (disabled or no webhook URL)', [
                'status_code' => $statusCode,
                'url' => $url,
            ]);
            return;
        }

        // Rate limiting: avoid spam for duplicate errors
        $cacheKey = $this->getErrorCacheKey($statusCode, $url, $exception);
        $shouldSend = $this->cache->get($cacheKey, function (ItemInterface $item) {
            $item->expiresAfter($this->rateLimitSeconds);
            return true;
        });

        if (!$shouldSend) {
            $this->logger->debug('Slack error notification rate-limited', [
                'status_code' => $statusCode,
                'url' => $url,
                'cache_key' => $cacheKey,
            ]);
            return;
        }

        $message = $this->buildErrorMessage($statusCode, $url, $referrer, $exception, $context);
        $this->sendToSlack($this->errorsWebhookUrl, $message, 'error');
    }

    /**
     * Send user registration notification to Slack registrations channel
     *
     * @param string $username The username/email of the new user
     * @param string $email The user's email
     * @param array $context Additional context (registration time, user agent, etc.)
     */
    public function notifyRegistration(
        string $username,
        string $email,
        array $context = []
    ): void {
        if (!$this->enabled || empty($this->registrationsWebhookUrl)) {
            $this->logger->debug('Slack registration notification skipped (disabled or no webhook URL)');
            return;
        }

        $message = $this->buildRegistrationMessage($username, $email, $context);
        $this->sendToSlack($this->registrationsWebhookUrl, $message, 'registration');
    }

    /**
     * Build Slack message payload for error notifications
     */
    private function buildErrorMessage(
        int $statusCode,
        string $url,
        ?string $referrer,
        ?\Throwable $exception,
        array $context
    ): array {
        $color = match (true) {
            $statusCode === 404 => '#FFA500', // Orange for 404
            $statusCode >= 500 => '#FF0000',  // Red for 5xx
            default => '#FFCC00'               // Yellow for other errors
        };

        $emoji = match (true) {
            $statusCode === 404 => ':mag:',
            $statusCode >= 500 => ':rotating_light:',
            default => ':warning:'
        };

        $title = match (true) {
            $statusCode === 404 => '404 Not Found',
            $statusCode >= 500 => "{$statusCode} Server Error",
            default => "HTTP {$statusCode} Error"
        };

        $fields = [
            [
                'title' => 'URL',
                'value' => $url,
                'short' => false,
            ],
            [
                'title' => 'Status Code',
                'value' => (string) $statusCode,
                'short' => true,
            ],
            [
                'title' => 'Environment',
                'value' => $context['environment'] ?? 'unknown',
                'short' => true,
            ],
        ];

        if ($referrer) {
            $fields[] = [
                'title' => 'Referrer',
                'value' => $referrer,
                'short' => false,
            ];
        }

        if ($exception) {
            $fields[] = [
                'title' => 'Exception',
                'value' => get_class($exception) . ': ' . $exception->getMessage(),
                'short' => false,
            ];

            $fields[] = [
                'title' => 'File',
                'value' => $exception->getFile() . ':' . $exception->getLine(),
                'short' => false,
            ];

            // Add first 3 lines of stack trace
            $trace = $exception->getTraceAsString();
            $traceLines = explode("\n", $trace);
            $shortTrace = implode("\n", array_slice($traceLines, 0, 3));

            $fields[] = [
                'title' => 'Stack Trace (first 3 lines)',
                'value' => "```{$shortTrace}```",
                'short' => false,
            ];
        }

        if (isset($context['user_agent'])) {
            $fields[] = [
                'title' => 'User Agent',
                'value' => $context['user_agent'],
                'short' => false,
            ];
        }

        if (isset($context['ip'])) {
            $fields[] = [
                'title' => 'IP Address',
                'value' => $context['ip'],
                'short' => true,
            ];
        }

        if (isset($context['timestamp'])) {
            $fields[] = [
                'title' => 'Timestamp',
                'value' => $context['timestamp'],
                'short' => true,
            ];
        }

        return [
            'text' => "{$emoji} *{$title}*",
            'attachments' => [
                [
                    'color' => $color,
                    'fields' => $fields,
                    'footer' => 'HeyBuddies Error Monitor',
                    'ts' => time(),
                ],
            ],
        ];
    }

    /**
     * Build Slack message payload for registration notifications
     */
    private function buildRegistrationMessage(
        string $username,
        string $email,
        array $context
    ): array {
        $fields = [
            [
                'title' => 'Username',
                'value' => $username,
                'short' => true,
            ],
            [
                'title' => 'Email',
                'value' => $email,
                'short' => true,
            ],
        ];

        if (isset($context['environment'])) {
            $fields[] = [
                'title' => 'Environment',
                'value' => $context['environment'],
                'short' => true,
            ];
        }

        if (isset($context['timestamp'])) {
            $fields[] = [
                'title' => 'Registration Time',
                'value' => $context['timestamp'],
                'short' => true,
            ];
        }

        if (isset($context['ip'])) {
            $fields[] = [
                'title' => 'IP Address',
                'value' => $context['ip'],
                'short' => true,
            ];
        }

        if (isset($context['user_agent'])) {
            $fields[] = [
                'title' => 'User Agent',
                'value' => $context['user_agent'],
                'short' => false,
            ];
        }

        return [
            'text' => ':tada: *New User Registration*',
            'attachments' => [
                [
                    'color' => '#36a64f', // Green for new registrations
                    'fields' => $fields,
                    'footer' => 'HeyBuddies Registration Monitor',
                    'ts' => time(),
                ],
            ],
        ];
    }

    /**
     * Send message to Slack webhook
     */
    private function sendToSlack(string $webhookUrl, array $payload, string $type): void
    {
        try {
            $response = $this->httpClient->request('POST', $webhookUrl, [
                'json' => $payload,
                'timeout' => 5,
            ]);

            $statusCode = $response->getStatusCode();

            if ($statusCode !== 200) {
                $this->logger->error('Slack notification failed', [
                    'type' => $type,
                    'status_code' => $statusCode,
                    'response' => $response->getContent(false),
                ]);
            } else {
                $this->logger->info('Slack notification sent successfully', [
                    'type' => $type,
                ]);
            }
        } catch (\Throwable $e) {
            $this->logger->error('Failed to send Slack notification', [
                'type' => $type,
                'error' => $e->getMessage(),
                'webhook_url' => substr($webhookUrl, 0, 30) . '...', // Partial URL for security
            ]);
        }
    }

    /**
     * Generate cache key for error rate limiting
     */
    private function getErrorCacheKey(int $statusCode, string $url, ?\Throwable $exception): string
    {
        $key = sprintf('slack_error_%d_%s', $statusCode, md5($url));

        if ($exception) {
            $key .= '_' . md5(get_class($exception) . $exception->getMessage());
        }

        return $key;
    }

    /**
     * Check if Slack notifications are enabled
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * Check if errors webhook is configured
     */
    public function hasErrorsWebhook(): bool
    {
        return !empty($this->errorsWebhookUrl);
    }

    /**
     * Check if registrations webhook is configured
     */
    public function hasRegistrationsWebhook(): bool
    {
        return !empty($this->registrationsWebhookUrl);
    }
}
