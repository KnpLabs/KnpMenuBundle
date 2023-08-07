<?php

namespace Knp\Bundle\MenuBundle\Templating\Helper;

use Knp\Menu\ItemInterface;
use Knp\Menu\Matcher\MatcherInterface;
use Knp\Menu\Twig\Helper;
use Knp\Menu\Util\MenuManipulator;
use Symfony\Component\Templating\Helper\Helper as TemplatingHelper;

class MenuHelper extends TemplatingHelper
{
    public function __construct(private Helper $helper, private MatcherInterface $matcher, private MenuManipulator $menuManipulator)
    {}

    /**
     * Retrieves an item following a path in the tree.
     *
     * @param \Knp\Menu\ItemInterface|string $menu
     *
     * @return \Knp\Menu\ItemInterface
     */
    public function get($menu, array $path = [], array $options = [])
    {
        return $this->helper->get($menu, $path, $options);
    }

    /**
     * Renders a menu with the specified renderer.
     *
     * @param \Knp\Menu\ItemInterface|string|array $menu
     * @param string                               $renderer
     *
     * @return string
     */
    public function render($menu, array $options = [], $renderer = null)
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
     * A string representation of this menu item.
     *
     * e.g. Top Level 1 > Second Level > This menu
     *
     * @param string $separator
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
     * @return bool
     */
    public function isCurrent(ItemInterface $item)
    {
        return $this->matcher->isCurrent($item);
    }

    /**
     * Checks whether an item is the ancestor of a current item.
     *
     * @param int $depth The max depth to look for the item
     *
     * @return bool
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
