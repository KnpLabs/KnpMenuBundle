<?php
namespace Knp\Bundle\MenuBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

/**
 * This compiler pass registers the renderers in the RendererProvider.
 *
 * @author Christophe Coevoet <stof@notk.org>
 */
class AddRenderersPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('knp_menu.renderer_provider')) {
            return;
        }
        $definition = $container->getDefinition('knp_menu.renderer_provider');

        $renderers = array();
        foreach ($container->findTaggedServiceIds('knp_menu.renderer') as $id => $tags) {
            foreach ($tags as $attributes) {
                if (empty($attributes['alias'])) {
                    throw new \InvalidArgumentException(sprintf('The alias is not defined in the "knp_menu.renderer" tag for the service "%s"', $id));
                }
                $renderers[$attributes['alias']] = $id;
            }
        }
        $definition->replaceArgument(2, $renderers);
    }
}
