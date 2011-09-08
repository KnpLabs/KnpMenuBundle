<?php
namespace Knp\Bundle\MenuBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

/**
 * This compiler pass registers the providers in the ChainProvider.
 *
 * @author Christophe Coevoet <stof@notk.org>
 */
class AddProvidersPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('knp_menu.menu_provider.chain')) {
            return;
        }

        $providers = array();
        foreach ($container->findTaggedServiceIds('knp_menu.provider') as $id => $tags) {
            $providers[] = new Reference($id);
        }

        if (1 === count($providers)) {
            // Use an alias instead of wrapping it in the ChainProvider for performances
            // when using only one (the default case as the bundle defines one provider)
            $container->setAlias('knp_menu.menu_provider', (string) reset($providers));
        } else {
            $definition = $container->getDefinition('knp_menu.menu_provider.chain');
            $definition->replaceArgument(0, $providers);
            $container->setAlias('knp_menu.menu_provider', 'knp_menu.menu_provider.chain');
        }
    }
}
