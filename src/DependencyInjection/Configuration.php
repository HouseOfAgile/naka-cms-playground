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
                ->end() // end internationalization

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
                ->end() // end openai_config

                ->booleanNode('maintenance_mode')
                    ->info('Enable or disable global maintenance mode')
                    ->defaultFalse()
                ->end()

                ->booleanNode('future_maintenance_warning')
                    ->info('Enable or disable a scheduled maintenance warning')
                    ->defaultFalse()
                ->end()

                ->scalarNode('maintenance_start')
                    ->info('Date/Time (string) when maintenance begins')
                    ->defaultNull()
                ->end()

                ->integerNode('maintenance_duration')
                    ->info('Duration of scheduled maintenance in minutes')
                    ->defaultValue(0)
                ->end()

                ->arrayNode('access_control')
                    ->info('Granular access control for platform features')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('restricted_routes')
                            ->info('Route-specific access control')
                            ->useAttributeAsKey('name')
                            ->booleanPrototype()
                                ->defaultTrue()
                            ->end()
                            ->defaultValue([
                                'app_login' => true,
                                'app_register' => true,
                                'booking_new' => false,
                            ])
                        ->end()
                    ->end()
                ->end() // end access_control
            ->end();

        return $treeBuilder;
    }
}
