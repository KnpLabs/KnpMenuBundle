<?php

namespace Bundle\MenuBundle;

use Bundle\MenuBundle\DependencyInjection\MenuExtension;
use Symfony\Framework\Bundle\Bundle as BaseBundle;
use Symfony\Components\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Components\DependencyInjection\ContainerBuilder;

class MenuBundle extends BaseBundle
{

    public function buildContainer(ParameterBagInterface $parameterBag)
    {
        ContainerBuilder::registerExtension(new MenuExtension());
    }

}
