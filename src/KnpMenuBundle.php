<?php

namespace Knp\Bundle\MenuBundle;

use Knp\Bundle\MenuBundle\DependencyInjection\Compiler\AddExtensionsPass;
use Knp\Bundle\MenuBundle\DependencyInjection\Compiler\AddProvidersPass;
use Knp\Bundle\MenuBundle\DependencyInjection\Compiler\AddRenderersPass;
use Knp\Bundle\MenuBundle\DependencyInjection\Compiler\AddVotersPass;
use Knp\Bundle\MenuBundle\DependencyInjection\Compiler\MenuBuilderPass;
use Knp\Bundle\MenuBundle\DependencyInjection\Compiler\MenuPass;
use Knp\Bundle\MenuBundle\DependencyInjection\Compiler\RegisterMenusPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class KnpMenuBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new RegisterMenusPass());
        $container->addCompilerPass(new MenuPass());
        $container->addCompilerPass(new MenuBuilderPass());
        $container->addCompilerPass(new AddExtensionsPass());
        $container->addCompilerPass(new AddProvidersPass());
        $container->addCompilerPass(new AddRenderersPass());
        $container->addCompilerPass(new AddVotersPass());
    }
}
