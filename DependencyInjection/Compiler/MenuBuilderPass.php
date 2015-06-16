<?php

namespace Knp\Bundle\MenuBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

/**
 * This compiler pass registers the menu builders in the BuilderServiceProvider.
 *
 * @author Christophe Coevoet <stof@notk.org>
 */
class MenuBuilderPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $definition = $container->getDefinition('knp_menu.menu_provider.builder_service');

        $menuBuilders = array();
        foreach ($container->findTaggedServiceIds('knp_menu.menu_builder') as $id => $tags) {
            foreach ($tags as $attributes) {
                if (empty($attributes['alias'])) {
                    throw new \InvalidArgumentException(sprintf('The alias is not defined in the "knp_menu.menu_builder" tag for the service "%s"', $id));
                }
                if (empty($attributes['method'])) {
                    throw new \InvalidArgumentException(sprintf('The method is not defined in the "knp_menu.menu_builder" tag for the service "%s"', $id));
                }
                $menuBuilders[$attributes['alias']] = array($id, $attributes['method']);
            }
        }
        $definition->replaceArgument(1, $menuBuilders);
    }
}
