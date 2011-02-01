<?php

namespace Bundle\MenuBundle;

use Bundle\MenuBundle\DependencyInjection\Compiler\MenuPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle as BaseBundle;

class MenuBundle extends BaseBundle
{
    public function registerExtensions(ContainerBuilder $container)
    {
        parent::registerExtensions($container);
        $container->addCompilerPass(new MenuPass());
    }

    public function getNamespace()
    {
        return __NAMESPACE__;
    }

    public function getPath()
    {
        return strtr(__DIR__, '\\', '/');
    }
}
