<?php
namespace Bundle\MenuBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class MenuPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('menu.manager')) {
            return;
        }
        $definition = $container->getDefinition('menu.manager');

        foreach ($container->findTaggedServiceIds('menu') as $id => $attributes) {
            if (isset($attributes[0]['alias'])) {
                $definition->addMethodCall('addMenu', array($attributes[0]['alias'], new Reference($id)));
            }
        }
    }
}
