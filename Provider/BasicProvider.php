<?php

namespace Knplabs\Bundle\MenuBundle\Provider;
use Knplabs\Bundle\MenuBundle\ProviderInterface;

class BasicProvider implements ProviderInterface
{
    protected $menus = array();

    public function addMenu($name, $menu)
    {
        $this->menus[$name] = $menu;
    }

    public function getMenu($name)
    {
        if(!isset($this->menus[$name])) {
            throw new \InvalidArgumentException(sprintf(
                'Menu "%s" does not exist. Available menus are %s',
                $name, implode(', ', array_keys($this->menus))
            ));
        }

        return $this->menus[$name];
    }
}
