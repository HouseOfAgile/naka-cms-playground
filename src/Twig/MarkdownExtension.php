<?php

namespace HouseOfAgile\NakaCMSBundle\Twig;

use HouseOfAgile\NakaCMSBundle\Service\MarkdownService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class MarkdownExtension extends AbstractExtension
{
    private MarkdownService $markdownService;

    public function __construct(MarkdownService $markdownService)
    {
        $this->markdownService = $markdownService;
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('markdown_to_html', [$this, 'convertMarkdownToHtml'], ['is_safe' => ['html']]),
        ];
    }

    public function convertMarkdownToHtml(string $markdown): string
    {
        return $this->markdownService->convert($markdown);
    }
}
