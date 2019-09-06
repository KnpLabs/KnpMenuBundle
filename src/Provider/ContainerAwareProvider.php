<?php

namespace Knp\Bundle\MenuBundle\Provider;

use Knp\Menu\ItemInterface;
use Knp\Menu\Provider\MenuProviderInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class ContainerAwareProvider implements MenuProviderInterface
{
    private $container;
    private $menuIds;

    public function __construct(ContainerInterface $container, array $menuIds = [])
    {
        $this->container = $container;
        $this->menuIds = $menuIds;
    }

    public function get(string $name, array $options = []): ItemInterface
    {
        if (!isset($this->menuIds[$name])) {
            throw new \InvalidArgumentException(sprintf('The menu "%s" is not defined.', $name));
        }

        return $this->container->get($this->menuIds[$name]);
    }

    public function has(string $name, array $options = []): bool
    {
        return isset($this->menuIds[$name]);
    }
}
