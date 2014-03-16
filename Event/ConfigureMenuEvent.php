<?php
namespace Knp\Bundle\MenuBundle\Event;

use Knp\Menu\Provider\MenuProviderInterface;
use Symfony\Component\EventDispatcher\Event;
use Knp\Menu\ItemInterface;

class ConfigureMenuEvent extends Event
{
    protected $builderName;
    protected $provider;
    protected $menu;

    public function __construct (MenuProviderInterface $provider, ItemInterface $menu, $builderName)
    {
        $this->builderName = $builderName;
        $this->provider = $provider;
        $this->menu = $menu;
    }

    /**
     * @return string
     */
    public function getBuilderName()
    {
        return $this->builderName;
    }

    /**
     * @return MenuProviderInterface
     */
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * @return ItemInterface
     */
    public function getMenu()
    {
        return $this->menu;
    }
}
