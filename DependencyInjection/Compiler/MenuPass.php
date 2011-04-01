<?php
namespace Knplabs\Bundle\MenuBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class MenuPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('menu.provider')) {
            return;
        }
        $definition = $container->getDefinition('menu.provider');

        $menus = array();
        foreach ($container->findTaggedServiceIds('menu') as $id => $attributes) {
            if (isset($attributes[0]['alias'])) {
                $definition->addMethodCall('addMenuServiceId', array($attributes[0]['alias'], $id));
            }
        }
    }
}
