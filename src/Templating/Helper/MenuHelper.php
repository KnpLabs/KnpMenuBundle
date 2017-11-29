<?php

namespace Knp\Bundle\MenuBundle\Templating\Helper;

use Symfony\Component\Templating\Helper\Helper as TemplatingHelper;
use Knp\Menu\Matcher\MatcherInterface;
use Knp\Menu\ItemInterface;
use Knp\Menu\Twig\Helper;
use Knp\Menu\Util\MenuManipulator;

class MenuHelper extends TemplatingHelper
{
    private $helper;
    private $matcher;
    private $menuManipulator;

    /**
     * @param Helper $helper
     */
    public function __construct(Helper $helper, MatcherInterface $matcher, MenuManipulator $menuManipulator)
    {
        $this->helper = $helper;
        $this->matcher = $matcher;
        $this->menuManipulator = $menuManipulator;
    }

    /**
     * Retrieves an item following a path in the tree.
     *
     * @param \Knp\Menu\ItemInterface|string $menu
     * @param array                          $path
     * @param array                          $options
     *
     * @return \Knp\Menu\ItemInterface
     */
    public function get($menu, array $path = array(), array $options = array())
    {
        return $this->helper->get($menu, $path, $options);
    }

    /**
     * Renders a menu with the specified renderer.
     *
     * @param \Knp\Menu\ItemInterface|string|array $menu
     * @param array                                $options
     * @param string                               $renderer
     *
     * @return string
     */
    public function render($menu, array $options = array(), $renderer = null)
    {
        return $this->helper->render($menu, $options, $renderer);
    }

    /**
     * Returns an array ready to be used for breadcrumbs.
     *
     * @param ItemInterface|array|string $menu
     * @param string|array|null          $subItem
     *
     * @return array
     */
    public function getBreadcrumbsArray($menu, $subItem = null)
    {
        return $this->helper->getBreadcrumbsArray($menu, $subItem);
    }

    /**
     * A string representation of this menu item
     *
     * e.g. Top Level 1 > Second Level > This menu
     *
     * @param ItemInterface $menu
     * @param string        $separator
     *
     * @return string
     */
    public function getPathAsString(ItemInterface $menu, $separator = ' > ')
    {
        return $this->menuManipulator->getPathAsString($menu, $separator);
    }

    /**
     * Checks whether an item is current.
     *
     * @param ItemInterface $item
     *
     * @return boolean
     */
    public function isCurrent(ItemInterface $item)
    {
        return $this->matcher->isCurrent($item);
    }

    /**
     * Checks whether an item is the ancestor of a current item.
     *
     * @param ItemInterface $item
     * @param integer       $depth The max depth to look for the item
     *
     * @return boolean
     */
    public function isAncestor(ItemInterface $item, $depth = null)
    {
        return $this->matcher->isAncestor($item, $depth);
    }

    /**
     * Returns the current item of a menu.
     *
     * @param ItemInterface|array|string $menu
     *
     * @return ItemInterface|null
     */
    public function getCurrentItem($menu)
    {
        return $this->helper->getCurrentItem($menu);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'knp_menu';
    }
}
