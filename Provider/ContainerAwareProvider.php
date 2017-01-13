<?php

namespace Knp\Bundle\MenuBundle\Provider;

use Knp\Menu\Provider\MenuProviderInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Knp\Menu\ItemInterface;

class ContainerAwareProvider implements MenuProviderInterface
{
    private $container;
    private $menuIds;

    public function __construct(ContainerInterface $container, array $menuIds = array())
    {
        $this->container = $container;
        $this->menuIds = $menuIds;
    }

    public function get($name, array $options = array())
    {
        if (!isset($this->menuIds[$name])) {
            throw new \InvalidArgumentException(sprintf('The menu "%s" is not defined.', $name));
        }

        $menu = $this->container->get($this->menuIds[$name]);

        if (!$menu instanceof ItemInterface) {
            throw new \LogicException(sprintf('The menu "%s" must be an ItemInterface object (%s given)', $name, $this->varToString($menu)));
        }

        return $menu;
    }

    public function has($name, array $options = array())
    {
        return isset($this->menuIds[$name]);
    }

    private function varToString($var)
    {
        if (is_object($var)) {
            return sprintf('Object(%s)', get_class($var));
        }

        if (is_array($var)) {
            $a = array();
            foreach ($var as $k => $v) {
                $a[] = sprintf('%s => %s', $k, $this->varToString($v));
            }

            return sprintf("Array(%s)", implode(', ', $a));
        }

        if (is_resource($var)) {
            return sprintf('Resource(%s)', get_resource_type($var));
        }

        if (null === $var) {
            return 'null';
        }

        if (false === $var) {
            return 'false';
        }

        if (true === $var) {
            return 'true';
        }

        return (string) $var;
    }
}
