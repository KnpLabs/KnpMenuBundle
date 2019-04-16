<?php
namespace Knp\Bundle\MenuBundle\DependencyInjection\Compiler;

use function call_user_func_array;
use function krsort;
use Symfony\Component\DependencyInjection\Argument\IteratorArgument;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

/**
 * This compiler pass registers the providers in the ChainProvider.
 *
 * @author Christophe Coevoet <stof@notk.org>
 *
 * @internal
 */
class AddProvidersPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('knp_menu.menu_provider.chain')) {
            return;
        }

        $providers = [];
        foreach ($container->findTaggedServiceIds('knp_menu.provider') as $id => $tags) {
            // Process only the first tag. Registering the same provider multiple time
            // does not make any sense, and this allows user to overwrite the tag added
            // by the autoconfiguration to change the priority (autoconfigured tags are
            // always added at the end of the list).
            $tag = $tags[0];

            $priority = isset($tag['priority']) ? (int) $tag['priority'] : 0;
            $providers[$priority][] = new Reference($id);
        }

        krsort($providers);
        $sortedProviders = call_user_func_array('array_merge', $providers);

        if (1 === count($sortedProviders)) {
            // Use an alias instead of wrapping it in the ChainProvider for performances
            // when using only one (the default case as the bundle defines one provider)
            $container->setAlias('knp_menu.menu_provider', (string) reset($sortedProviders));
        } else {
            if (class_exists(IteratorArgument::class)) {
                $sortedProviders = new IteratorArgument($sortedProviders);
            }

            $definition = $container->getDefinition('knp_menu.menu_provider.chain');
            $definition->replaceArgument(0, $sortedProviders);
            $container->setAlias('knp_menu.menu_provider', 'knp_menu.menu_provider.chain');
        }
    }
}
