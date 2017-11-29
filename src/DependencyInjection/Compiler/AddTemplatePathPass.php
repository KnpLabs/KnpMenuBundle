<?php
namespace Knp\Bundle\MenuBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

/**
 * This compiler pass adds the path for the KnpMenu template in the twig loader.
 *
 * @author Christophe Coevoet <stof@notk.org>
 *
 * @internal
 */
class AddTemplatePathPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $loaderDefinition = null;

        if ($container->hasDefinition('twig.loader.filesystem')) {
            $loaderDefinition = $container->getDefinition('twig.loader.filesystem');
        }

        if (null === $loaderDefinition) {
            return;
        }

        $refl = new \ReflectionClass('Knp\Menu\ItemInterface');
        $path = dirname($refl->getFileName()).'/Resources/views';
        $loaderDefinition->addMethodCall('addPath', array($path));
    }
}
