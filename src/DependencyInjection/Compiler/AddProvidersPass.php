<?php

namespace Knp\Bundle\MenuBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * This compiler pass registers the ChainProvider or the only provider as `knp_menu.menu_provider`.
 *
 * @author Christophe Coevoet <stof@notk.org>
 *
 * @internal
 */
final class AddProvidersPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $providers = [];
        foreach ($container->findTaggedServiceIds('knp_menu.provider') as $id => $tags) {
            $providers[] = $id;
        }

        if (1 === \count($providers)) {
            // Use an alias instead of wrapping it in the ChainProvider for performances
            // when using only one (the default case as the bundle defines one provider)
            $container->setAlias('knp_menu.menu_provider', $providers[0]);
        } else {
            $container->setAlias('knp_menu.menu_provider', 'knp_menu.menu_provider.chain');
        }
    }
}
