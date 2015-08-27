<?php

namespace Knp\Bundle\MenuBundle\DependencyInjection;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;

class KnpMenuExtension extends Extension
{
    /**
     * Handles the knp_menu configuration.
     *
     * @param array            $configs   The configurations being loaded
     * @param ContainerBuilder $container
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('menu.xml');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        foreach ($config['providers'] as $builder => $enabled) {
            if ($enabled) {
                $container->getDefinition(sprintf('knp_menu.menu_provider.%s', $builder))->addTag('knp_menu.provider');
            }
        }

        if (isset($config['twig'])) {
            $loader->load('twig.xml');
            $container->setParameter('knp_menu.renderer.twig.template', $config['twig']['template']);
        }
        if ($config['templating']) {
            $loader->load('templating.xml');
        }

        $container->setParameter('knp_menu.default_renderer', $config['default_renderer']);
    }

    /**
     * {@inheritdoc}
     */
    public function getNamespace()
    {
        return 'http://knplabs.com/schema/dic/menu';
    }

    /**
     * {@inheritdoc}
     */
    public function getXsdValidationBasePath()
    {
        return __DIR__ . '/../Resources/config/schema';
    }
}
