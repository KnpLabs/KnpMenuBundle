<?php

namespace Knp\Bundle\MenuBundle\DependencyInjection;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;

class KnpMenuExtension extends Extension
{
    private $loader = null;

    /**
     * Handles the knp_menu configuration.
     *
     * @param array $configs The configurations being loaded
     * @param ContainerBuilder $container
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $this->loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $this->loadProvider($configs, $container);
        $this->loadFactory($configs, $container);

        $this->loader->load('menu.xml');

        if ($config['twig']) {
            $this->loader->load('twig.xml');
        }
        if ($config['templating']) {
            $this->loader->load('templating.xml');
        }

        $container->setParameter('knp_menu.scan_container_for_menus', $config['scan_container_for_menus']);
    }

    protected function loadProvider($configs, $container)
    {
        $this->loader->load('provider.xml');
    }

    protected function loadFactory($configs, $container)
    {
        $this->loader->load('factory.xml');
    }
}
