<?php

namespace HouseOfAgile\NakaCMSBundle\Component\ApiService\OpenAI;

use HouseOfAgile\NakaCMSBundle\Exception\OpenAIException;
use HouseOfAgile\NakaCMSBundle\Service\CacheKeyGenerator;
use OpenAI;
use OpenAI\Client;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class OpenAIService
{
    private LoggerInterface $logger;
    protected $devMode;
    private CacheInterface $cache;
    private CacheKeyGenerator $cacheKeyGenerator;
    private Client $openaiClient;
    private array $prompts;
    private int $defaultWordCount;
    private string $additionalInstructions;
    private string $defaultModel;

    public function __construct(
        LoggerInterface $loggerApiService,
        bool $devMode,
        CacheInterface $cache,
        CacheKeyGenerator $cacheKeyGenerator,
        ?array $openaiConfig,
        $openaiApiKey
    ) {
        $this->logger = $loggerApiService;
        $this->devMode = $devMode;
        $this->cache = $cache;
        $this->cacheKeyGenerator = $cacheKeyGenerator;
        $this->openaiClient = OpenAI::client($openaiApiKey);

        // Handle case when openai_prompts is not defined
        if ($openaiConfig) {
            $this->prompts = $openaiConfig['prompts'] ?? [];
            $this->defaultWordCount = $openaiConfig['default_word_count'] ?? 500;
            $this->additionalInstructions = $openaiConfig['additional_instructions'] ?? '';
            $this->defaultModel = $openaiConfig['default_model'] ?? 'gpt-4o-mini';
        } else {
            $this->prompts = [];
            $this->defaultWordCount = 500;
            $this->additionalInstructions = '';
            $this->defaultModel = 'gpt-4o-mini';
        }
    }

    public function getFirstNamePresentation(string $firstName, string $promptKey): string
    {
        if (!isset($this->prompts[$promptKey])) {
            throw new \InvalidArgumentException("Prompt not found: " . $promptKey);
        }

        $promptTemplate = $this->prompts[$promptKey];
        $prompt = str_replace("{firstname}", $firstName, $promptTemplate);

        if (!empty($this->additionalInstructions)) {
            $prompt .= " " . $this->additionalInstructions;
        }

        $cacheKey = $this->cacheKeyGenerator->generate('openai_name_description', [$firstName, $promptKey]);
        return $this->cache->get($cacheKey, function (ItemInterface $item) use ($prompt) {
            $item->expiresAfter(3600); // Cache for 1 hour

            $maxTokens = $this->defaultWordCount * 2.2; // Rough estimation of tokens to words ratio

            $response = $this->openaiClient->chat()->create([
                'model' => $this->defaultModel,
                'messages' => [
                    ['role' => 'user', 'content' => $prompt]
                ],
                'max_tokens' => $maxTokens,
            ]);


            if ($this->devMode && isset($response['usage'])) {
                $this->trackCurrentUsage($response['usage']);
            }

            if (isset($response['choices']) && count($response['choices']) > 0) {
                $result = $response['choices']['0'];
                if ($result['finish_reason'] != 'stop' && $result['finish_reason'] != 'length') {
                    throw new OpenAIException(sprintf('We have an interesting finish_reason: %s', $result['finish_reason']));
                }

                return $result['message']['content'];
            } else {
                throw new OpenAIException('We have no result: No choices in response.');
            }
        });
    }

    public function trackCurrentUsage($usage): void
    {
        try {
            $this->logger->info('OpenAI API usage', $usage);
            return;

            $detailedUsage = sprintf(
                "Current Open AI Usage\nPrompt Tokens: %s\nCompletion Tokens: %s\nTotal Tokens: %s",
                $usage->promptTokens,
                $usage->completionTokens,
                $usage->totalTokens
            );
            $this->logger->info($detailedUsage);
        } catch (OpenAIException $e) {
            $this->logger->error('Error retrieving OpenAI API usage', ['error' => $e->getMessage()]);
        }
    }
}
