<?php

namespace Knp\Bundle\MenuBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Argument\ServiceClosureArgument;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * This compiler pass registers the menu builders in the LazyProvider.
 *
 * @author Christophe Coevoet <stof@notk.org>
 *
 * @internal
 */
final class RegisterMenusPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition('knp_menu.menu_provider.lazy')) {
            return;
        }

        // Remove the old way of handling this feature.
        $container->removeDefinition('knp_menu.menu_provider.builder_service');

        $menuBuilders = [];
        foreach ($container->findTaggedServiceIds('knp_menu.menu_builder', true) as $id => $tags) {
            foreach ($tags as $attributes) {
                if (empty($attributes['alias'])) {
                    throw new \InvalidArgumentException(sprintf('The alias is not defined in the "knp_menu.menu_builder" tag for the service "%s"', $id));
                }
                if (empty($attributes['method'])) {
                    throw new \InvalidArgumentException(sprintf('The method is not defined in the "knp_menu.menu_builder" tag for the service "%s"', $id));
                }
                $menuBuilders[$attributes['alias']] = [new ServiceClosureArgument(new Reference($id)), $attributes['method']];
            }
        }

        foreach ($container->findTaggedServiceIds('knp_menu.menu', true) as $id => $tags) {
            foreach ($tags as $attributes) {
                if (empty($attributes['alias'])) {
                    throw new \InvalidArgumentException(sprintf('The alias is not defined in the "knp_menu.menu" tag for the service "%s"', $id));
                }
                $menuBuilders[$attributes['alias']] = new ServiceClosureArgument(new Reference($id));
            }
        }

        $container->getDefinition('knp_menu.menu_provider.lazy')->replaceArgument(0, $menuBuilders);
    }
}
