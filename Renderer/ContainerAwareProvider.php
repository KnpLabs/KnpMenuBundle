<?php

namespace Knp\Bundle\MenuBundle\Renderer;

use Knp\Menu\Renderer\RendererProviderInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ContainerAwareProvider implements RendererProviderInterface
{
    private $container;
    private $rendererIds;

    public function __construct(ContainerInterface $container, array $rendererIds = array())
    {
        $this->container = $container;
        $this->rendererIds = $rendererIds;
    }

    public function get($name)
    {
        if (!isset($this->rendererIds[$name])) {
            throw new \InvalidArgumentException(sprintf('The renderer "%s" is not defined.', $name));
        }

        return $this->container->get($this->rendererIds[$name]);
    }

    public function has($name)
    {
        return isset($this->rendererIds[$name]);
    }
}
