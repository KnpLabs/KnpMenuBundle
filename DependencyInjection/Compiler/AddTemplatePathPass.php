<?php
namespace Knp\Bundle\MenuBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

/**
 * This compiler pass adds the path for the KnpMenu template in the twig loader.
 *
 * @author Christophe Coevoet <stof@notk.org>
 */
class AddTemplatePathPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('twig.loader')) {
            return;
        }
        $refl = new \ReflectionClass('Knp\Menu\ItemInterface');
        $path = dirname($refl->getFileName()).'/Resources/views';
        $container->getDefinition('twig.loader')->addMethodCall('addPath', array($path));
    }
}
