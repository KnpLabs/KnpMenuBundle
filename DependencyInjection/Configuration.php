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
            ->addDefaultsIfNotSet()
            ->children()
                ->booleanNode('twig')->defaultTrue()->end()
                ->booleanNode('templating')->defaultFalse()->end()
                ->booleanNode('scan_container_for_menus')->defaultTrue()->end()
            ->end();

        return $treeBuilder;
    }
}
