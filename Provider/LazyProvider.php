<?php

namespace Knplabs\MenuBundle\Provider;

use Knplabs\MenuBundle\ProviderInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LazyProvider implements ProviderInterface
{
    protected $container;

    protected $menuServiceIds = array();

    public function addMenuServiceId($name, $serviceId)
    {
        $this->menuServiceIds[$name] = $serviceId;
    }

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getMenu($name)
    {
        if (!isset($this->menuServiceIds[$name])) {
            throw new \InvalidArgumentException(sprintf('The menu "%s" is not defined.', $name));
        }

        return $this->container->get($this->menuServiceIds[$name]);
    }
}
