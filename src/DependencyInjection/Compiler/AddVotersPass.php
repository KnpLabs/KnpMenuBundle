<?php

namespace Knp\Bundle\MenuBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Argument\IteratorArgument;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * This compiler pass registers the voters in the Matcher.
 *
 * @author Christophe Coevoet <stof@notk.org>
 *
 * @internal
 * @final
 */
final class AddVotersPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition('knp_menu.matcher')) {
            return;
        }

        $definition = $container->getDefinition('knp_menu.matcher');

        $voters = [];

        foreach ($container->findTaggedServiceIds('knp_menu.voter') as $id => $tags) {
            // Process only the first tag. Registering the same voter multiple time
            // does not make any sense, and this allows user to overwrite the tag added
            // by the autoconfiguration to change the priority (autoconfigured tags are
            // always added at the end of the list).
            $tag = $tags[0];

            $priority = isset($tag['priority']) ? (int) $tag['priority'] : 0;
            $voters[$priority][] = new Reference($id);
        }

        if (empty($voters)) {
            return;
        }

        krsort($voters);
        $sortedVoters = array_merge(...$voters);

        $definition->replaceArgument(0, new IteratorArgument($sortedVoters));
    }
}
