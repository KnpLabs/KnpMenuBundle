<?php

namespace Knp\Bundle\MenuBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Knp\Bundle\MenuBundle\DependencyInjection\Compiler\MenuPass;
use Knp\Bundle\MenuBundle\DependencyInjection\Compiler\AddProvidersPass;
use Knp\Bundle\MenuBundle\DependencyInjection\Compiler\AddRenderersPass;
use Knp\Bundle\MenuBundle\DependencyInjection\Compiler\AddTemplatePathPass;
use Knp\Bundle\MenuBundle\DependencyInjection\Compiler\AddVotersPass;

class KnpMenuBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new MenuPass());
        $container->addCompilerPass(new AddProvidersPass());
        $container->addCompilerPass(new AddRenderersPass());
        $container->addCompilerPass(new AddTemplatePathPass());
        $container->addCompilerPass(new AddVotersPass());
    }
}
