<?php
namespace Knp\Bundle\MenuBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class MenuPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('knp_menu.menu_provider.container_aware')) {
            return;
        }
        $definition = $container->getDefinition('knp_menu.menu_provider.container_aware');

        $menus = array();
        foreach ($container->findTaggedServiceIds('knp_menu.menu') as $id => $tags) {
            foreach ($tags as $attributes) {
                if (empty($attributes['alias'])) {
                    throw new \InvalidArgumentException(sprintf('The alias is not defined in the "knp_menu.menu" tag for the service "%s"', $id));
                }
                $menus[$attributes['alias']] = $id;
            }
        }
        $definition->replaceArgument(1, $menus);
    }
}
