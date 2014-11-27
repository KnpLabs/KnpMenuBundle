<?php

namespace Knp\Bundle\MenuBundle\DependencyInjection;

use Knp\Bundle\MenuBundle\Expression\ExpressionEvaluator;
use Knp\Bundle\MenuBundle\Expression\ExpressionLanguage;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
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
        $exprEvalClass = $container->getParameter('knp_menu.expression_evaluator.class');
        $variableNames = call_user_func(array($exprEvalClass, 'getVariableNames'));
        $exprLangClass = $container->getParameter('knp_menu.expression_language.class');
        $exprLang = new $exprLangClass();

        if (!$exprLang instanceof ExpressionLanguage) {
            throw new InvalidArgumentException('Parameter "knp_menu.expression_language.class" must be an instance of "Knp\Bundle\MenuBundle\Expression\ExpressionLanguage"');
        }

        array_walk_recursive(
            $menus,
            function (&$value, $key) use ($exprLang, $variableNames) {
                if (0 === strpos($value, ExpressionEvaluator::EXPRESSION_PREFIX) || in_array($key, array('show_if', 'hide_if'))) {
                    $value = ExpressionEvaluator::EXPRESSION_PREFIX.serialize($exprLang->parse(ltrim($value, ExpressionEvaluator::EXPRESSION_PREFIX), $variableNames)->getNodes());
                }
            }
        );

        return $menus;
    }
}
