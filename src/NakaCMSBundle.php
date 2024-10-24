<?php

namespace HouseOfAgile\NakaCMSBundle;

use HouseOfAgile\NakaCMSBundle\DependencyInjection\Compiler\OpenAICompilerPass;
use HouseOfAgile\NakaCMSBundle\DependencyInjection\NakaCMSExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class NakaCMSBundle extends Bundle
{
    /**
     * Overridden to allow for the custom extension alias.
     */
    public function getContainerExtension(): ?ExtensionInterface
    {
        if (null === $this->extension) {
            $this->extension = new NakaCMSExtension();
        }
        return $this->extension;
    }

    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        // Register the OpenAICompilerPass
        $container->addCompilerPass(new OpenAICompilerPass());
    }
}
