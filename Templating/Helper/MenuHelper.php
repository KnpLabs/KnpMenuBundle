<?php

namespace Knp\Bundle\MenuBundle\Templating\Helper;

use Symfony\Component\Templating\Helper\Helper;
use Knp\Menu\Renderer\RendererProviderInterface;
use Knp\Menu\Provider\MenuProviderInterface;
use Knp\Menu\ItemInterface;

class MenuHelper extends Helper
{
    private $rendererProvider;
    private $menuProvider;

    /**
     * @param \Knp\Menu\Renderer\RendererProviderInterface $rendererProvider
     * @param \Knp\Menu\Provider\MenuProviderInterface|null $menuProvider
     */
    public function __construct(RendererProviderInterface $rendererProvider, MenuProviderInterface $menuProvider = null)
    {
        $this->rendererProvider = $rendererProvider;
        $this->menuProvider = $menuProvider;
    }

    /**
     * @param string $name
     * @return \Knp\Menu\ItemInterface
     * @throws \InvalidArgumentException
     */
    public function get($name)
    {
        if (null === $this->menuProvider) {
            throw new \BadMethodCallException('A menu provider must be set to retrieve a menu');
        }

        return $this->menuProvider->get($name);
    }

    /**
     * Renders a menu with the specified renderer.
     *
     * @param \Knp\Menu\ItemInterface|string $menu
     * @param string $renderer
     * @param array $options
     * @return string
     */
    public function render($menu, $renderer, array $options = array())
    {
        if (!$menu instanceof ItemInterface) {
            $menu = $this->get($menu);
        }

        return $this->rendererProvider->get($renderer)->render($menu, $options);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'knp_menu';
    }
}
