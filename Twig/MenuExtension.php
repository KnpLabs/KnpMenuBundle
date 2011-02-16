<?php

namespace Bundle\MenuBundle\Twig;

use Bundle\MenuBundle\MenuManager;

class MenuExtension extends \Twig_Extension
{
    /**
     * @var MenuManager
     */
    protected $manager;

    /**
     * @param MenuManager
     */
    public function __construct(MenuManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return array(
            'menu' => new \Twig_Function_Method($this, 'render', array(
                'is_safe' => array('html'),
            )),
        );
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
     * @param string $name
     * @param integer $depth (optional)
     * @return string
     */
    public function render($name, $depth = null)
    {
        return $this->get($name)->render($depth);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'menu';
    }
}
