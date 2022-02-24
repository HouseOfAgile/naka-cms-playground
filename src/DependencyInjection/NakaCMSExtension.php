<?php

namespace HouseOfAgile\NakaCMSBundle\DependencyInjection;

use EasyCorp\Bundle\EasyAdminBundle\DependencyInjection\EasyAdminExtension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class NakaCMSExtension extends EasyAdminExtension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.xml');
        parent::load($configs, $container);

        // $configuration = $this->getConfiguration($configs, $container);
        // $config = $this->processConfiguration($configuration, $configs);

        // $definition = $container->getDefinition('hoa_naka_cms.knpu_ipsum');
        // $definition->setArgument(0, $config['unicorns_are_real']);
        // $definition->setArgument(1, $config['min_sunshine']);
    }

    public function getAlias()
    {
        return 'hoa_naka_cms';
    }
}
