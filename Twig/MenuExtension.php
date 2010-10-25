<?php

namespace Bundle\MenuBundle\Twig;

use Symfony\Component\DependencyInjection\ContainerInterface;

use Bundle\MenuBundle\Twig\MenuTokenParser;

class MenuExtension extends \Twig_Extension
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
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        $this->menus = array();
        foreach ($this->container->findTaggedServiceIds('menu') as $id => $attributes) {
            if (isset($attributes[0]['alias'])) {
                $this->menus[$attributes[0]['alias']] = $id;
            }
        }
    }

    /**
     * @return array
     */
    public function getTokenParsers()
    {
        return array(
            // {% menu "name" %}
            new MenuTokenParser(),
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
