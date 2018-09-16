<?php

namespace Knp\Bundle\MenuBundle\DependencyInjection\Compiler;

use Knp\Bundle\MenuBundle\MenuBuilderProviderInterface;
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
class RegisterMenusPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('knp_menu.menu_provider.lazy')) {
            return;
        }

        // When using Symfony < 3.3, the LazyProvider cannot be used (at least not in a lazy way)
        // so the older providers will be used.
        if (!class_exists(ServiceClosureArgument::class)) {
            $container->removeDefinition('knp_menu.menu_provider.lazy');

            return;
        }

        // Remove the old way of handling this feature.
        $container->removeDefinition('knp_menu.menu_provider.container_aware');
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

        foreach ($container->findTaggedServiceIds('knp_menu.menu_builder_provider', true) as $id => $tags) {
            $def = $container->getDefinition($id);

            // We must assume that the class value has been correctly filled, even if the service is created by a factory
            $class = $container->getParameterBag()->resolveValue($def->getClass());
            $interface = MenuBuilderProviderInterface::class;

            if (!is_subclass_of($class, $interface)) {
                if (!class_exists($class, false)) {
                    throw new \InvalidArgumentException(sprintf('Class "%s" used for service "%s" cannot be found.', $class, $id));
                }

                throw new \InvalidArgumentException(sprintf('Service "%s" must implement interface "%s".', $id, $interface));
            }

            $container->addObjectResource($class);

            foreach ($class::getMenuBuilders() as $alias => $method) {
                $menuBuilders[$alias] = [new ServiceClosureArgument(new Reference($id)), $method];
            }
        }

        $container->getDefinition('knp_menu.menu_provider.lazy')->replaceArgument(0, $menuBuilders);
    }
}
