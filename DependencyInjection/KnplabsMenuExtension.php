<?php

namespace Knplabs\Bundle\MenuBundle\DependencyInjection;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;

class KnplabsMenuExtension extends Extension
{
    /**
     * Handles the knplabs_menu configuration.
     *
     * @param  array $configs The configurations being loaded
     * @param ContainerBuilder $container
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('provider.xml');

        $config = array();
        foreach($configs as $c) {
            $config = array_merge($config, $c);
        }

        if(!empty($config['twig'])) {
            $loader->load('twig.xml');
            $loader->load('templating.xml');
        } elseif(!empty($config['templating'])) {
            $loader->load('templating.xml');
        }
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
        return 'http://symfony.com/schema/dic/menu';
    }

    /**
     * @see Symfony\Component\DependencyInjection\Extension\ExtensionInterface
     */
    public function getAlias()
    {
        return 'knplabs_menu';
    }
}
