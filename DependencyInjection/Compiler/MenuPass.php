<?php
namespace Bundle\MenuBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class MenuPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if ($container->hasDefinition('twig')) {
            if (!$container->hasDefinition('twig.extension.menu')) {
                return;
            }
        }
    	
        if ($container->hasDefinition('templating')) {
            if (!$container->hasDefinition('templating.helper.menu')) {
                return;
            }
        }

        $menus = array();
        
        foreach ($container->findTaggedServiceIds('menu') as $id => $attributes) {
            if (isset($attributes[0]['alias'])) {
                $menus[$attributes[0]['alias']] = $id;
            }
        }
        
        $container->setParameter('menu.services', $menus);
    }
}