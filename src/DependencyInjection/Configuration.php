<?php

namespace HouseOfAgile\NakaCMSBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('hoa_naka_cms');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->arrayNode('internationalization')
                    ->children()
                        ->arrayNode('all_locales')
                            ->defaultValue(['en', 'de'])
                            ->scalarPrototype()->end()
                        ->end()
                        ->scalarNode('supported_locales')
                            ->info('String representing the supported locales separated by \'|\'')
                            ->defaultValue('en|de')
                        ->end()
                    ->end()
                ->end()
                ->scalarNode('redirect_url')
                    ->info('The route to redirect to after login')
                    ->defaultValue('app_homepage')
                ->end()
                
                ->arrayNode('openai_config')  
                    ->info('Configuration for OpenAI API')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('default_word_count')
                            ->info('Default word count for OpenAI prompts')
                            ->defaultValue(500)
                        ->end()
                        ->scalarNode('additional_instructions')
                            ->info('Additional instructions to be appended to each prompt')
                            ->defaultValue('Make sure the description is engaging and informative.')
                        ->end()
                        ->arrayNode('prompts')
                            ->useAttributeAsKey('name')
                            ->scalarPrototype()
                                ->defaultValue('who are you.')
                            ->end()
                            ->defaultValue([
                                'prompt1' => 'how are you ?',
                                'prompt2' => 'what time is it ?',
                            ])
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
