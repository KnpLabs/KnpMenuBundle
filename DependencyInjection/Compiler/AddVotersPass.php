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
        
        $voters = array();

        foreach ($container->findTaggedServiceIds('knp_menu.voter') as $id => $tags) {
            // Process only the first tag. Registering the same voter multiple time
            // does not make any sense, and this allows user to overwrite the tag added
            // by the autoconfiguration to change the priority (autoconfigured tags are
            // always added at the end of the list).
            $tag = $tags[0];

            $priority = isset($tag['priority']) ? (int)$tag['priority'] : 0;
            $voters[$priority][] = $id;

            if (isset($tag['request']) && $tag['request']) {
                $voterDefinition = $container->getDefinition($id);
                $voterDefinition->addArgument(new Reference('request_stack'));
            }

        }
        if (empty($voters)) {
            return;
        }

        krsort($voters);
        $sortedVoters = call_user_func_array('array_merge', $voters);

        $sortedVotersReferences = [];
        foreach ($sortedVoters as $id) {
            $sortedVotersReferences[] = new Reference($id);
        }

        $definition = $container->getDefinition('knp_menu.matcher');
        $definition->addArgument($sortedVotersReferences);
    }
}
