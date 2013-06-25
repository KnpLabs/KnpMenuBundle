<?php

namespace Knp\Bundle\MenuBundle;

use Knp\Bundle\MenuBundle\DependencyInjection\Compiler\AddExtensionsPass;
use Knp\Bundle\MenuBundle\DependencyInjection\Compiler\AddProvidersPass;
use Knp\Bundle\MenuBundle\DependencyInjection\Compiler\AddRenderersPass;
use Knp\Bundle\MenuBundle\DependencyInjection\Compiler\AddTemplatePathPass;
use Knp\Bundle\MenuBundle\DependencyInjection\Compiler\AddVotersPass;
use Knp\Bundle\MenuBundle\DependencyInjection\Compiler\MenuPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class KnpMenuBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new MenuPass());
        $container->addCompilerPass(new AddExtensionsPass());
        $container->addCompilerPass(new AddProvidersPass());
        $container->addCompilerPass(new AddRenderersPass());
        $container->addCompilerPass(new AddTemplatePathPass());
        $container->addCompilerPass(new AddVotersPass());
    }
}
