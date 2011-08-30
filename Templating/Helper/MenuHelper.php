<?php

namespace Knp\Bundle\MenuBundle\Templating\Helper;

use Symfony\Component\Templating\Helper\Helper as TemplatingHelper;
use Knp\Menu\Twig\Helper;

class MenuHelper extends TemplatingHelper
{
    private $helper;

    /**
     * @param \Knp\Menu\Twig\Helper $helper
     */
    public function __construct(Helper $helper)
    {
        $this->helper = $helper;
    }

    /**
     * Retrieves a menu from the menu provider.
     *
     * @param string $name
     * @return \Knp\Menu\ItemInterface
     */
    public function get($name)
    {
        return $this->helper->get($name);
    }

    /**
     * Retrieves an item following a path in the tree.
     *
     * @param \Knp\Menu\ItemInterface|string $menu
     * @param array $path
     * @return \Knp\Menu\ItemInterface
     */
    public function getByPath($menu, array $path)
    {
        return $this->helper->getByPath($menu, $path);
    }

    /**
     * Renders a menu with the specified renderer.
     *
     * @param \Knp\Menu\ItemInterface|string|array $menu
     * @param string $renderer
     * @param array $options
     * @return string
     */
    public function render($menu, $renderer, array $options = array())
    {
        return $this->helper->render($menu, $renderer, $options);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'knp_menu';
    }
}
