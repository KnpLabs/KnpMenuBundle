<?php
namespace Knp\Bundle\MenuBundle\DependencyInjection\Compiler;

use Knp\Menu\Renderer\PsrProvider;
use Symfony\Component\DependencyInjection\Compiler\ServiceLocatorTagPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

/**
 * This compiler pass registers the renderers in the RendererProvider.
 *
 * @author Christophe Coevoet <stof@notk.org>
 *
 * @internal
 */
class AddRenderersPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('knp_menu.renderer_provider')) {
            return;
        }

        $renderers = array();
        $rendererReferences = array();

        foreach ($container->findTaggedServiceIds('knp_menu.renderer', true) as $id => $tags) {
            foreach ($tags as $attributes) {
                if (empty($attributes['alias'])) {
                    throw new \InvalidArgumentException(sprintf('The alias is not defined in the "knp_menu.renderer" tag for the service "%s"', $id));
                }
                $renderers[$attributes['alias']] = $id;
                $rendererReferences[$attributes['alias']] = new Reference($id);
            }
        }

        if (class_exists(ServiceLocatorTagPass::class)) {
            $locator = ServiceLocatorTagPass::register($container, $rendererReferences);
            // Replace the service definition with a PsrProvider
            $container->register('knp_menu.renderer_provider', PsrProvider::class)
                ->addArgument($locator)
                ->addArgument('%knp_menu.default_renderer%');
        } else {
            // BC for Symfony < 3.3
            $definition = $container->getDefinition('knp_menu.renderer_provider');
            $definition->replaceArgument(2, $renderers);
        }
    }
}
