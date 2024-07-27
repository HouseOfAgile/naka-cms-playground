<?php

namespace HouseOfAgile\NakaCMSBundle\Service;

use League\CommonMark\CommonMarkConverter;

class MarkdownService
{
    private CommonMarkConverter $converter;

    public function __construct()
    {
        $this->converter = new CommonMarkConverter([
            'html_input' => 'strip', // Use 'allow' if you want to allow HTML in the Markdown
            'allow_unsafe_links' => false,
        ]);
    }

    public function convert(string $markdown): string
    {
        return $this->converter->convert($markdown)->getContent();
    }
}
