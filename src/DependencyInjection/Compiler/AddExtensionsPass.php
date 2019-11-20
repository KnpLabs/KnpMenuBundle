<?php

namespace Knp\Bundle\MenuBundle\DependencyInjection\Compiler;

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

/**
 * This compiler pass registers the renderers in the RendererProvider.
 *
 * @author Christophe Coevoet <stof@notk.org>
 *
 * @internal
 * @final
 */
final class AddExtensionsPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->has('knp_menu.factory')) {
            return;
        }

        $taggedServiceIds = $container->findTaggedServiceIds('knp_menu.factory_extension');
        if (0 === \count($taggedServiceIds)) {
            return;
        }

        $definition = $container->findDefinition('knp_menu.factory');

        if (!method_exists($container->getParameterBag()->resolveValue($definition->getClass()), 'addExtension')) {
            throw new InvalidConfigurationException(sprintf(
                'To use factory extensions, the service of class "%s" registered as knp_menu.factory must implement the "addExtension" method',
                $definition->getClass()
            ));
        }

        foreach ($taggedServiceIds as $id => $tags) {
            foreach ($tags as $tag) {
                $priority = isset($tag['priority']) ? $tag['priority'] : 0;
                $definition->addMethodCall('addExtension', [new Reference($id), $priority]);
            }
        }
    }
}
