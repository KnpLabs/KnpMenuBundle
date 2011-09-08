<?php

namespace Knp\Bundle\MenuBundle\Renderer;

use Knp\Menu\Renderer\RendererProviderInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ContainerAwareProvider implements RendererProviderInterface
{
    private $container;
    private $rendererIds;
    private $defaultRenderer;

    public function __construct(ContainerInterface $container, $defaultRenderer, array $rendererIds)
    {
        $this->container = $container;
        $this->rendererIds = $rendererIds;
        $this->defaultRenderer = $defaultRenderer;
    }

    public function get($name = null)
    {
        if (null === $name) {
            $name = $this->defaultRenderer;
        }

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
