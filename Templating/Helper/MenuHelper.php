<?php

namespace Bundle\MenuBundle\Templating\Helper;

use Symfony\Component\Templating\Helper\Helper;
use Bundle\MenuBundle\MenuItem;

class MenuHelper extends Helper implements \ArrayAccess
{
    protected $menu;
    protected $name;

    /**
     * Constructor.
     *
     * @param MenuItem $menu a Menu
     * @param string $name the canonical name of the helper
     */
    public function __construct(MenuItem $menu, $name)
    {
        $this->menu = $menu;
        $this->name = $name;
    }

    /**
     * Render the menu
     *
     * @return string
     */
    public function render()
    {
        return $this->menu->render();
    }

    /**
     * Get the menu
     *
     * @return MenuItem
     **/
    public function get()
    {
        return $this->menu;
    }

    /**
     * Implements ArrayAccess
     */
    public function offsetExists($name)
    {
        $this->menu->offsetExists($name);
    }

    /**
     * Implements ArrayAccess
     */
    public function offsetGet($name)
    {
        return $this->menu->offsetGet($name);
    }

    /**
     * Implements ArrayAccess
     */
    public function offsetSet($name, $value)
    {
        return $this->menu->offsetSet($name, $value);
    }

    /**
     * Implements ArrayAccess
     */
    public function offsetUnset($name)
    {
        $this->menu->offsetUnset($name);
    }

    /**
     * Returns the canonical name of this helper.
     *
     * @return string The canonical name
     */
    public function getName()
    {
        return $this->name;
    }

    public function __toString()
    {
        return $this->render();
    }

    /**
     * Method deferring
     *
     * @param  string $method
     * @param  array $args
     * @return mixed
     * @throws Exception if the menu does not have the required method
     */
    public function __call($method, $args)
    {
        if (!method_exists($this->menu, $method)) {
            throw new \Exception('Menu has no method '.$method);
        }

        return call_user_func_array(array($this->menu, $method), $args);
    }
}
