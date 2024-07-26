<?php

namespace HouseOfAgile\NakaCMSBundle\Component\ApiService\OpenAI;

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
        } else {
            $this->prompts = [];
            $this->defaultWordCount = 500;
            $this->additionalInstructions = '';
        }
    }

    public function getNameDescription(string $firstName, string $promptKey): string
    {
        if (!isset($this->prompts[$promptKey])) {
            throw new \InvalidArgumentException("Prompt not found: " . $promptKey);
        }

        $promptTemplate = $this->prompts[$promptKey];
        $prompt = str_replace("{firstname}", $firstName, $promptTemplate);

        if (!empty($this->additionalInstructions)) {
            $prompt .= " " . $this->additionalInstructions;
        }

        $cacheKey = $this->cacheKeyGenerator->generate('openai_name_description', [$firstName, $promptKey], true);

        return $this->cache->get($cacheKey, function (ItemInterface $item) use ($prompt) {
            $item->expiresAfter(3600); // Cache for 1 hour

            $maxTokens = $this->defaultWordCount * 1.5; // Rough estimation of tokens to words ratio

            $response = $this->openaiClient->completions()->create([
                'model' => 'gpt-4o-mini',
                'prompt' => $prompt,
                'max_tokens' => $maxTokens,
            ]);

            dd($response);
            $text = $response['choices'][0]['text'] ?? 'No response';
            // Format the response into paragraphs
            $paragraphs = explode("\n", $text);
            $formattedText = implode("\n\n", array_map('trim', $paragraphs));

            return $formattedText;
        });
    }
}
