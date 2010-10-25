<?php

namespace Bundle\MenuBundle\Templating\Helper;

use Symfony\Component\Templating\Helper\Helper;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Bundle\MenuBundle\MenuItem;

class MenuHelper extends Helper implements \ArrayAccess
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;

    /**
     * @var array
     */
    protected $menus;

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     * @return void
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        $this->menus = array();
        foreach ($this->container->findTaggedServiceIds('menu') as $id => $attributes) {
            if (isset($attributes[0]['alias'])) {
                $this->menus[$attributes[0]['alias']] = $id;
            }
        }
    }

    /**
     * Render the menu
     *
     * @param string $name
     * @param integer $depth (optional)
     * @return string
     */
    public function render($name, $depth = null)
    {
        return $this->get($name)->render($depth);
    }

    /**
     * @param string $name
     * @return \Bundle\MenuBundle\Menu
     * @throws \InvalidArgumentException
     */
    public function get($name)
    {
        if (!isset($this->menus[$name])) {
            throw new \InvalidArgumentException(sprintf('The menu "%s" is not defined.', $name));
        }

        if (is_string($this->menus[$name])) {
            $this->menus[$name] = $this->container->get($this->menus[$name]);
        }

        return $this->menus[$name];
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'menu';
    }

    /**
     * Implements ArrayAccess
     */
    public function offsetExists($name)
    {
        return isset($this->menus[$name]);
    }

    /**
     * Implements ArrayAccess
     */
    public function offsetGet($name)
    {
        return $this->get($name);
    }

    /**
     * Implements ArrayAccess
     */
    public function offsetSet($name, $value)
    {
        return $this->menus[$name] = $value;
    }

    /**
     * Implements ArrayAccess
     */
    public function offsetUnset($name)
    {
        throw new \LogicException(sprintf('You can\'t unset a menu from a template (%s).', $id));
    }
}
