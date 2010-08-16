<?php

namespace Bundle\MenuBundle;

/**
 * A convenience class for creating the root node of a menu.
 * Decoupled from Symfony2, can be used in any PHP 5.3 project.
 * Originally taken from ioMenuPlugin (http://github.com/weaverryan/ioMenuPlugin)
 *
 * When creating the root menu object, you can use this class or the
 * normal MenuItem class. For example, the following are equivalent:
 *   $menu = new Menu(array('class' => 'root'));
 *   $menu = new MenuItem(null, null, array('class' => 'root'));
 */
class Menu extends MenuItem
{
    /**
     * @var string
     */
    protected $childClass;

    /**
     * Class constructor
     * 
     * @see MenuItem
     * @param array   $attributes
     * @param string  $childClass The class to use if instantiating children menu items
     */
    public function __construct($attributes = array(), $childClass = 'Bundle\MenuBundle\MenuItem')
    {
        $this->childClass = $childClass;

        parent::__construct(null, null, $attributes);
    }

    /**
     * Overridden to specify what the child class should be
     */
    protected function createChild($name, $route = null, $attributes = array(), $class = null)
    {
        if (null === $class)
        {
            $class = $this->childClass;
        }

        return parent::createChild($name, $route, $attributes, $class);
    }

    /**
     * Get the class used to instanciate children
     *
     * @return string
     **/
    public function getChildClass()
    {
        return $this->childClass;
    }
}
