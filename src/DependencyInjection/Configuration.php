<?php

namespace HouseOfAgile\NakaCMSBundle\DependencyInjection;

use EasyCorp\Bundle\EasyAdminBundle\DependencyInjection\Configuration as BaseConfiguration;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration extends BaseConfiguration
{
    // public function getConfigTreeBuilder()
    // {
    //     $treeBuilder = new TreeBuilder('hoa_naka_cms');
    //     $rootNode = $treeBuilder->getRootNode();
    //     $rootNode
    //         ->children()
    //         ->booleanNode('unicorns_are_real')->defaultTrue()->info('Whether or not you believe in unicorns')->end()
    //         ->integerNode('min_sunshine')->defaultValue(3)->info('How much do you like sunshine?')->end()
    //         ->end();
    //     return $treeBuilder;
    // }
}
