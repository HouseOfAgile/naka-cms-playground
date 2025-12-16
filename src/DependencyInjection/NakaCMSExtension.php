<?php
namespace HouseOfAgile\NakaCMSBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class NakaCMSExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.xml');

        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        // Set i18n parameters
        $container->setParameter(
            'hoa_naka_cms.internationalization.all_locales',
            $config['internationalization']['all_locales']
        );
        $container->setParameter(
            'hoa_naka_cms.internationalization.supported_locales',
            $config['internationalization']['supported_locales']
        );

        // Set app.supported_locales parameter to match hoa_naka_cms.internationalization.supported_locales
        // This ensures routing and other components use the correct locales without 'us' suffix
        $container->setParameter('app.supported_locales', $config['internationalization']['supported_locales']);

        // Set redirect URL
        $container->setParameter('hoa_naka_cms.redirect_url', $config['redirect_url']);

        // Set OpenAI configuration
        $container->setParameter('hoa_naka_cms.openai_config.default_word_count', $config['openai_config']['default_word_count']);
        $container->setParameter('hoa_naka_cms.openai_config.additional_instructions', $config['openai_config']['additional_instructions']);
        $container->setParameter('hoa_naka_cms.openai_config.prompts', $config['openai_config']['prompts']);

        // Set maintenance & access control
        $container->setParameter('hoa_naka_cms.maintenance_mode', $config['maintenance_mode']);
        $container->setParameter('hoa_naka_cms.access_control', $config['access_control']);

        // NEW: Future maintenance warning
        $container->setParameter('hoa_naka_cms.future_maintenance_warning', $config['future_maintenance_warning']);
        // NEW: Maintenance start & duration
        $container->setParameter('hoa_naka_cms.maintenance_start', $config['maintenance_start']);
        $container->setParameter('hoa_naka_cms.maintenance_duration', $config['maintenance_duration']);
    }

    public function getConfiguration(array $config, ContainerBuilder $container)
    {
        return new Configuration();
    }

    public function getAlias(): string
    {
        return 'hoa_naka_cms';
    }
}
