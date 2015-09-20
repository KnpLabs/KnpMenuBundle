<?php

namespace Knp\Bundle\MenuBundle\DependencyInjection;

use Knp\Bundle\MenuBundle\Expression\ExpressionContextInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
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

        if ($menus = $config['menus']) {
            $loader->load('config_provider.xml');

            if (class_exists('Symfony\Component\ExpressionLanguage\ExpressionLanguage')) {
                $loader->load('expression.xml');
                $menus = $this->preParseExpressions($menus, $container);
            }

            $container->getDefinition('knp_menu.menu_provider.config')->replaceArgument(0, $menus);
        }
    }

    public function getNamespace()
    {
        return 'http://knplabs.com/schema/dic/menu';
    }

    private function preParseExpressions(array $menus, ContainerBuilder $container)
    {
        $contextClass = $container->getParameter('knp_menu.expression_context.class');

        // get variable names
        $variableNames = call_user_func(array($contextClass, 'getNames'));

        array_walk_recursive(
            $menus,
            function (&$value, $key) use ($contextClass, $variableNames) {
                if (0 !== strpos($value, ExpressionContextInterface::EXPRESSION_PREFIX) && !in_array($key, array('show_if', 'hide_if'))) {
                    return;
                }

                $expression = ltrim($value, ExpressionContextInterface::EXPRESSION_PREFIX);
                $parsed = call_user_func(array($contextClass, 'parse'), $expression);

                $value = ExpressionContextInterface::EXPRESSION_PREFIX.serialize($parsed->getNodes());
            }
        );

        return $menus;
    }
}
