<?php

namespace Knp\Bundle\MenuBundle\Twig;

use Knp\Bundle\MenuBundle\Templating\Helper\MenuHelper;

class MenuExtension extends \Twig_Extension
{
    /**
     * @var MenuHelper
     */
    protected $helper;

    /**
     * @param MenuHelper
     */
    public function __construct(MenuHelper $helper)
    {
        $this->helper = $helper;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return array(
            'knp_menu' => new \Twig_Function_Method($this, 'render', array(
                'is_safe' => array('html'),
            )),
            'knp_menu_get' => new \Twig_Function_Method($this, 'get', array(
                'is_safe' => array('html'),
            )),
        );
    }

    /**
     * @param string $name
     * @return \Knp\Bundle\MenuBundle\Menu
     * @throws \InvalidArgumentException
     */
    public function get($name)
    {
        return $this->helper->get($name);
    }

    /**
     * @param string $name
     * @param integer $depth (optional)
     * @return string
     */
    public function render($name, $path = null, $depth = 1, $template = null)
    {
        return $this->helper->render($name, $path, $depth, $template);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'knp_menu';
    }
}
