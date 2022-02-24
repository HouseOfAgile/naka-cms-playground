<?php

namespace HouseOfAgile\NakaCMSBundle;

use EasyCorp\Bundle\EasyAdminBundle\EasyAdminBundle;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use HouseOfAgile\NakaCMSBundle\DependencyInjection\NakaCMSExtension;

class NakaCMSBundle extends EasyAdminBundle
{
    /**
     * Overridden to allow for the custom extension alias.
     */
    public function getContainerExtension()
    {
        if (null === $this->extension) {
            $this->extension = new NakaCMSExtension();
        }
        return $this->extension;
    }
}
