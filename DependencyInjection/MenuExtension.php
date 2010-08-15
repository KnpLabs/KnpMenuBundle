<?php

namespace Bundle\MenuBundle\DependencyInjection;

use Symfony\Components\DependencyInjection\Extension\Extension;
use Symfony\Components\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Components\DependencyInjection\ContainerBuilder;

class MenuExtension extends Extension
{

    public function templatingLoad($config, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, __DIR__.'/../Resources/config');
        $loader->load('templating.xml');
    }

    /**
     * Returns the base path for the XSD files.
     *
     * @return string The XSD base path
     */
    public function getXsdValidationBasePath()
    {
        return null;
    }

    public function getNamespace()
    {
        return 'http://www.symfony-project.org/schema/dic/symfony';
    }

    public function getAlias()
    {
        return 'menu';
    }

}
