<?php

namespace Bundle\MenuBundle\Templating\Helper;

use Symfony\Component\Templating\Helper\Helper;
use Bundle\MenuBundle\MenuManager;

class MenuHelper extends Helper implements \ArrayAccess
{
    /**
     * @var MenuManager
     */
    protected $manager;

    /**
     * @param MenuManager
     * @return void
     */
    public function __construct(MenuManager $manager)
    {
        $this->manager = $manager;
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
        return $this->manager->getMenu($name);
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
