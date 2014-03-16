<?php
namespace Knp\Bundle\MenuBundle\Provider;

use Knp\Bundle\MenuBundle\Event\ConfigureMenuEvent;
use Knp\Bundle\MenuBundle\Events;
use Knp\Menu\Provider\MenuProviderInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class EventEmittingProvider implements MenuProviderInterface
{
    protected $provider;
    protected $eventDispatcher;
    public function __construct (MenuProviderInterface $provider, EventDispatcherInterface $eventDispatcher)
    {
        $this->provider = $provider;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Retrieves a menu by its name
     *
     * @param string $name
     * @param array  $options
     *
     * @return \Knp\Menu\ItemInterface
     * @throws \InvalidArgumentException if the menu does not exists
     */
    public function get($name, array $options = array())
    {
        $menu = $this->provider->get($name, $options);

        $this->eventDispatcher->dispatch(Events::CONFIGURE, new ConfigureMenuEvent($this->provider, $menu, $name));

        return $menu;
    }

    /**
     * Checks whether a menu exists in this provider
     *
     * @param string $name
     * @param array  $options
     *
     * @return boolean
     */
    public function has($name, array $options = array())
    {
        return $this->provider->has($name, $options);
    }
}
