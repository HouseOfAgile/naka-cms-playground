<?php

namespace HouseOfAgile\NakaCMSBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class OpenAICompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasParameter('hoa_naka_cms.openai_config.default_word_count') ||
            !$container->hasParameter('hoa_naka_cms.openai_config.additional_instructions') ||
            !$container->hasParameter('hoa_naka_cms.openai_config.prompts')) {
            return;
        }

        $openaiConfig = [
            'default_word_count' => $container->getParameter('hoa_naka_cms.openai_config.default_word_count'),
            'additional_instructions' => $container->getParameter('hoa_naka_cms.openai_config.additional_instructions'),
            'prompts' => $container->getParameter('hoa_naka_cms.openai_config.prompts'),
        ];

        $container->setParameter('hoa_naka_cms.openai_config', $openaiConfig);
    }
}
