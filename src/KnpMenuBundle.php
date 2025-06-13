<?php

namespace Knp\Bundle\MenuBundle;

use Knp\Bundle\MenuBundle\DependencyInjection\Compiler\AddExtensionsPass;
use Knp\Bundle\MenuBundle\DependencyInjection\Compiler\AddProvidersPass;
use Knp\Bundle\MenuBundle\DependencyInjection\Compiler\RegisterMenusPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class KnpMenuBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new RegisterMenusPass());
        $container->addCompilerPass(new AddExtensionsPass());
        $container->addCompilerPass(new AddProvidersPass());
    }

    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}
