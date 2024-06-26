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
                ->defaultValue(['en','de','fr'])
                ->scalarPrototype()->end()
            ->end()
            ->scalarNode('supported_locales')
            ->info('String representing the supported locales seprated by \'|\'')
                ->defaultValue('en|de')
            ->end()
            ->end()
            ->end() // twitter
        ;

        return $treeBuilder;
    }
}
