<?php

namespace Knp\Bundle\MenuBundle\Provider;

use Knp\Menu\Provider\MenuProviderInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * This provider uses methods of services to build menus.
 *
 * @author Christophe Coevoet <stof@notk.org>
 */
class BuilderServiceProvider implements MenuProviderInterface
{
    private $container;
    private $menuBuilders;

    public function __construct(ContainerInterface $container, array $menuBuilders = array())
    {
        $this->container = $container;
        $this->menuBuilders = $menuBuilders;
    }

    public function get($name, array $options = array())
    {
        if (!isset($this->menuBuilders[$name])) {
            throw new \InvalidArgumentException(sprintf('The menu "%s" is not defined.', $name));
        }

        if (!is_array($this->menuBuilders[$name]) || 2 !== count($this->menuBuilders[$name])) {
            throw new \InvalidArgumentException(sprintf('The menu builder definition for the menu "%s" is invalid. It should be an array (serviceId, method)', $name));
        }

        list($id, $method) = $this->menuBuilders[$name];

        return $this->container->get($id)->$method($options);
    }

    public function has($name, array $options = array())
    {
        return isset($this->menuBuilders[$name]);
    }
}
