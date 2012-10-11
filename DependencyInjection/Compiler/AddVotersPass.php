<?php

namespace Knp\Bundle\MenuBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

/**
 * This compiler pass registers the renderers in the RendererProvider.
 *
 * @author Christophe Coevoet <stof@notk.org>
 */
class AddVotersPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('knp_menu.matcher')) {
            return;
        }

        $definition = $container->getDefinition('knp_menu.matcher');
        $listener = $container->getDefinition('knp_menu.listener.voters');

        foreach ($container->findTaggedServiceIds('knp_menu.voter') as $id => $tags) {
            $definition->addMethodCall('addVoter', array(new Reference($id)));

            foreach ($tags as $tag) {
                if (isset($tag['request']) && $tag['request']) {
                    $listener->addMethodCall('addVoter', array(new Reference($id)));
                }
            }
        }
    }
}
