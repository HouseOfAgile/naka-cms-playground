<?php

namespace HouseOfAgile\NakaCMSBundle;

use EasyCorp\Bundle\EasyAdminBundle\EasyAdminBundle;
use HouseOfAgile\NakaCMSBundle\DependencyInjection\NakaCMSExtension;
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
}
