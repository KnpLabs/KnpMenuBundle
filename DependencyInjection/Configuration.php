<?php

namespace Knp\Bundle\MenuBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This class contains the configuration information for the bundle
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
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('knp_menu');

        $rootNode
            ->fixXmlConfig('provider')
            ->children()
                ->arrayNode('providers')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('builder_alias')->defaultTrue()->end()
                        ->booleanNode('container_aware')->defaultTrue()->end()
                        ->booleanNode('builder_service')->defaultTrue()->end()
                    ->end()
                ->end()
                ->arrayNode('twig')
                    ->addDefaultsIfNotSet()
                    ->canBeUnset()
                    ->children()
                        ->scalarNode('template')->defaultValue('knp_menu.html.twig')->end()
                    ->end()
                ->end()
                ->booleanNode('templating')->defaultFalse()->end()
                ->scalarNode('default_renderer')->cannotBeEmpty()->defaultValue('twig')->end()
                ->variableNode('menus')
                    ->defaultNull()
                    ->validate()
                    ->ifTrue(function ($value) { return !is_array($value); })
                        ->thenInvalid('Must be an array.')
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
