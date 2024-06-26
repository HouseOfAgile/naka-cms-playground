<?php

namespace HouseOfAgile\NakaCMSBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class NakaCMSExtension extends Extension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.xml');

        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        $definition = $container->getDefinition('hoa_naka_cms.hoa_ipsum');
        $definition->setArgument(0, $config['internationalization']['all_locales']);
        $definition->setArgument(1, $config['internationalization']['supported_locales']);
    }

    public function prepend(ContainerBuilder $container): void
    {
        $thirdPartyBundlesViewFileLocator = (new FileLocator(__DIR__ . '/../Resources/views/bundles'));

        // Here we want to override some templates from easyadmin
        $container->loadFromExtension('twig', [
            'paths' => [
                $thirdPartyBundlesViewFileLocator->locate('EasyAdminBundle') => 'EasyAdmin',
            ],
        ]);
    }
    
    public function getAlias(): string
    {
        return 'hoa_naka_cms';
    }
}
