<?php

namespace Knp\Bundle\MenuBundle\Renderer;

use Knp\Menu\Renderer\RendererProviderInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @deprecated since version 2.2. Use "Knp\Menu\Renderer\PsrProvider" instead.
 */
class ContainerAwareProvider implements RendererProviderInterface
{
    private $container;
    private $rendererIds;
    private $defaultRenderer;

    public function __construct(ContainerInterface $container, $defaultRenderer, array $rendererIds, $triggerDeprecation = true)
    {
        $this->container = $container;
        $this->rendererIds = $rendererIds;
        $this->defaultRenderer = $defaultRenderer;

        if ($triggerDeprecation) {
            @trigger_error(sprintf('The %s class is deprecated since 2.2 and will be removed in 3.0. USe "Knp\Menu\Renderer\PsrProvider" instead.', __CLASS__),E_USER_DEPRECATED);
        }
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
