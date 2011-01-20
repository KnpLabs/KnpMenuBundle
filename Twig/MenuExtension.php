<?php

namespace Bundle\MenuBundle\Twig;

use Symfony\Component\DependencyInjection\ContainerInterface;

class MenuExtension extends \Twig_Extension
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;

    /**
     * @var array
     */
    protected $menus = array();

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     * @return void
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        $this->menus = $this->container->getParameter('menu.services');
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
        if (!isset($this->menus[$name])) {
            throw new \InvalidArgumentException(sprintf('The menu "%s" is not defined.', $name));
        }

        if (is_string($this->menus[$name])) {
            $this->menus[$name] = $this->container->get($this->menus[$name]);
        }

        return $this->menus[$name];
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
