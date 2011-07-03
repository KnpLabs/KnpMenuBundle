<?php
namespace Knp\Bundle\MenuBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class MenuPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('knp_menu.provider')) {
            return;
        }
        $definition = $container->getDefinition('knp_menu.provider');

        $menus = array();
        foreach ($container->findTaggedServiceIds('knp_menu.menu') as $id => $attributes) {
            if (isset($attributes[0]['alias'])) {
                $definition->addMethodCall('addMenuServiceId', array($attributes[0]['alias'], $id));
            }
        }
    }
}
