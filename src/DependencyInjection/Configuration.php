<?php

namespace Knp\Bundle\MenuBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This class contains the configuration information for the bundle.
 *
 * @author Christophe Coevoet <stof@notk.org>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree.
     *
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('knp_menu');

        // Keep compatibility with symfony/config < 4.2
        if (method_exists($treeBuilder, 'getRootNode')) {
            $rootNode = $treeBuilder->getRootNode();
        } else {
            $rootNode = $treeBuilder->root('knp_menu');
        }

        $rootNode
            ->children()
                ->arrayNode('providers')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('builder_alias')->defaultTrue()->end()
                    ->end()
                ->end()
                ->arrayNode('twig')
                    ->addDefaultsIfNotSet()
                    ->canBeUnset()
                    ->children()
                        ->scalarNode('template')->defaultValue('@KnpMenu/menu.html.twig')->end()
                    ->end()
                ->end()
                ->booleanNode('templating')->defaultFalse()->end()
                ->scalarNode('default_renderer')->cannotBeEmpty()->defaultValue('twig')->end()
            ->end();

        return $treeBuilder;
    }
}
