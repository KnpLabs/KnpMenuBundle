<?php
namespace Knp\Bundle\MenuBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class MenuPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->getParameter('knp_menu.scan_container_for_menus')) {
            return;
        }

        if (!$container->hasDefinition('knp_menu.menu_provider')) {
            return;
        }
        $definition = $container->getDefinition('knp_menu.menu_provider');

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
