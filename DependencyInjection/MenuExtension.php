<?php

namespace Bundle\MenuBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;

class MenuExtension extends Extension
{
    /**
     * Handles the menu.templating configuration.
     *
     * @param  array $config The configuration being loaded
     * @param ContainerBuilder $container
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('templating.xml');
        $loader->load('twig.xml');
    }

    /**
     * @see Symfony\Component\DependencyInjection\Extension\ExtensionInterface
     */
    public function getXsdValidationBasePath()
    {
        return __DIR__.'/../Resources/config/schema';
    }

    /**
     * @see Symfony\Component\DependencyInjection\Extension\ExtensionInterface
     */
    public function getNamespace()
    {
        return 'http://www.symfony-project.org/schema/dic/menu';
    }

    /**
     * @see Symfony\Component\DependencyInjection\Extension\ExtensionInterface
     */
    public function getAlias()
    {
        return 'menu';
    }
}
