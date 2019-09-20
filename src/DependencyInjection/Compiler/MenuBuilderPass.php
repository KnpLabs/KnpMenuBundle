<?php

namespace Knp\Bundle\MenuBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

/**
 * This compiler pass registers the menu builders in the BuilderServiceProvider.
 *
 * @author Christophe Coevoet <stof@notk.org>
 *
 * @internal
 */
final class MenuBuilderPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition('knp_menu.menu_provider.builder_service')) {
            return;
        }

        $definition = $container->getDefinition('knp_menu.menu_provider.builder_service');

        $menuBuilders = [];
        foreach ($container->findTaggedServiceIds('knp_menu.menu_builder') as $id => $tags) {
            $builderDefinition = $container->getDefinition($id);

            if (!$builderDefinition->isPublic()) {
                throw new \InvalidArgumentException(sprintf('Menu builder services must be public but "%s" is a private service.', $id));
            }

            if ($builderDefinition->isAbstract()) {
                throw new \InvalidArgumentException(sprintf('Abstract services cannot be registered as menu builders but "%s" is.', $id));
            }

            foreach ($tags as $attributes) {
                if (empty($attributes['alias'])) {
                    throw new \InvalidArgumentException(sprintf('The alias is not defined in the "knp_menu.menu_builder" tag for the service "%s"', $id));
                }
                if (empty($attributes['method'])) {
                    throw new \InvalidArgumentException(sprintf('The method is not defined in the "knp_menu.menu_builder" tag for the service "%s"', $id));
                }
                $menuBuilders[$attributes['alias']] = [$id, $attributes['method']];
            }
        }
        $definition->replaceArgument(1, $menuBuilders);
    }
}
