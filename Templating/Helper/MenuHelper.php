<?php

namespace Bundle\MenuBundle\Templating\Helper;

use Symfony\Component\Templating\Helper\Helper;
use Symfony\Component\Templating\Engine;
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
    public function __construct(ContainerInterface $container, Engine $engine)
    {
        $this->container = $container;
        $this->engine = $engine;

        $this->menus = array();
        foreach ($this->container->findTaggedServiceIds('menu') as $id => $attributes) {
            if (isset($attributes[0]['alias'])) {
                $this->menus[$attributes[0]['alias']] = $id;
            }
        }
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
        throw new \LogicException(sprintf('You can\'t unset a menu from a template (%s).', $name));
    }

    /**
     * Render the menu
     *
     * @param string $name
     * @param integer $path (optional)
     * @param integer $depth (optional)
     * @param string $template (optional)
     * @return string
     */
    public function render($name, $path = null, $depth = null, $template = null)
    {
        $item = $this->get($name);
        $item->initialize(array('path' => $path));
        
        return $this->doRender($item, $depth, $template);
    }

    /**
     * Renders menu tree. Internal method.
     *
     * @param MenuItem  $item        Menu item
     * @param integer $depth (optional)
     * @param string $template       The template name
     *
     * @return string
     */
    public function doRender(MenuItem $item, $depth = null, $template = null)
    {
        /**
         * Return an empty string if any of the following are true:
         *   a) The menu has no children eligible to be displayed
         *   b) The depth is 0
         *   c) This menu item has been explicitly set to hide its children
         */
        if (!$item->hasChildren() || $depth === 0 || !$item->getShowChildren()) {
            return '';
        }

        if (null === $template) {
            $template = 'MenuBundle:Menu:menu.php';
        }


        return trim($this->engine->render($template, array(
            'item'  => $item,
        )));
    }

    public function attributes($attributes)
    {
        if ($attributes instanceof \Traversable) {
            $attributes = iterator_to_array($attributes);
        }

        return implode('', array_map(array($this, 'attributesCallback'), array_keys($attributes), array_values($attributes)));
    }

    private function attribute($name, $value)
    {
        return sprintf('%s="%s"', $name, true === $value ? $name : $value);
    }

    /**
     * Prepares an attribute key and value for HTML representation.
     *
     * It removes empty attributes, except for the value one.
     *
     * @param  string $name   The attribute name
     * @param  string $value  The attribute value
     *
     * @return string The HTML representation of the HTML key attribute pair.
     */
    private function attributesCallback($name, $value)
    {
        if (false === $value || null === $value || ('' === $value && 'value' != $name)) {
            return '';
        } else {
            return ' '.$this->attribute($name, $value);
        }
    }

    /**
     *
     * @param MenuItem $menuItem
     * @param integer $depth The depth each child should render
     * @return array
     */
    public function getItemAttributes(MenuItem $item)
    {
        // if we don't have access or this item is marked to not be shown
        if (!$item->shouldBeRendered()) {
            return;
        }

        $depth = $item->getLevel();

        // explode the class string into an array of classes
        $class = ($item->getAttribute('class')) ? explode(' ', $item->getAttribute('class')) : array();

        if ($item->getIsCurrent()) {
            $class[] = 'current';
        }
        elseif ($item->getIsCurrentAncestor($depth)) {
            $class[] = 'current_ancestor';
        }

        if ($item->actsLikeFirst()) {
            $class[] = 'first';
        }
        if ($item->actsLikeLast()) {
            $class[] = 'last';
        }

        // retrieve the attributes and put the final class string back on it
        $attributes = $item->getAttributes();
        if (!empty($class)) {
            $attributes['class'] = implode(' ', $class);
        }

        return $attributes;
    }
}
