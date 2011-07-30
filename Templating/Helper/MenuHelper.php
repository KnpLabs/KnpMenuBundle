<?php

namespace Knp\Bundle\MenuBundle\Templating\Helper;

use Symfony\Component\Templating\Helper\Helper;
use Knp\Bundle\MenuBundle\ProviderInterface;
use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Knp\Bundle\MenuBundle\MenuItem;

class MenuHelper extends Helper implements \ArrayAccess
{
    /**
     * @var ProviderInterface
     */
    protected $provider;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @param ProviderInterface
     */
    public function __construct(ProviderInterface $provider, ContainerInterface $container)
    {
        $this->provider = $provider;
        $this->container = $container;
    }

    /**
     * @param string $name
     * @return \Knp\Bundle\MenuBundle\Menu
     * @throws \InvalidArgumentException
     */
    public function get($name)
    {
        $menu = $this->provider->getMenu($name);
        $menu->setCurrentUri($this->container->get('request')->getRequestUri());
        return $menu;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'knp_menu';
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
    public function render($name, $depth = null, $template = null)
    {
        $item = $this->get($name);

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
            $template = 'KnpMenuBundle:Menu:menu.html.twig';
        }


        return trim($this->container->get('templating')->render($template, array(
            'item'  => $item,
            'menu' => $this,
            'depth' => $depth,
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
