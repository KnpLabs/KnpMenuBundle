<?php

namespace Bundle\MenuBundle\Twig;

use Bundle\MenuBundle\ProviderInterface;

class MenuExtension extends \Twig_Extension
{
    /**
     * @var ProviderInterface
     */
    protected $provider;

    /**
     * @param ProviderInterface
     */
    public function __construct(ProviderInterface $provider)
    {
        $this->provider = $provider;
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
        return $this->provider->getMenu($name);
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
