Using events to allow a menu to be extended
===========================================

If you want to let different parts of your system hook into the building
of your menu, a good way is to use an approach based on the Symfony2 EventDispatcher
component.

## Create the menu builder

Your menu builder will create the base menu item and then dispatch an event
to allow other parts of your application to add more stuff to it.

```php
<?php
// src/Acme/DemoBundle/Menu/MainBuilder.php

namespace Acme\DemoBundle\Menu;

use Acme\DemoBundle\MenuEvents;
use Acme\DemoBundle\Event\ConfigureMenuEvent;
use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAware;

class MainBuilder extends ContainerAware
{
    public function build(FactoryInterface $factory)
    {
        $menu = $factory->createItem('root');

        $menu->setCurrentUri($this->container->get('request')->getRequestUri());
        $menu->addChild('Dashboard', array('route' => '_acp_dashboard'));

        $this->container->get('event_dispatcher')->dispatch(MenuEvents::CONFIGURE, new ConfigureMenuEvent($factory, $menu));

        return $menu;
    }
}
```

**Note:** This implementation assumes you use the BuilderAliasProvider (getting
your menu as ``AcmeDemoBundle:MainBuilder:build``) but you could also define
it as a service and inject the ``event_dispatcher`` service as a dependency.

## Create the Event object

The event object allows to pass some data to the listener. In this case,
it will hold the menu being created and the factory.

```php
<?php
// src/Acme/DemoBundle/Event/ConfigureMenuEvent.php

namespace Acme\DemoBundle\Event;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\EventDispatcher\Event;

class ConfigureMenuEvent extends Event
{
    const CONFIGURE = 'acme_demo.menu_configure';

    private $factory;
    private $menu;

    /**
     * @param \Knp\Menu\FactoryInterface $factory
     * @param \Knp\Menu\ItemInterface $menu
     */
    public function __construct(FactoryInterface $factory, ItemInterface $menu)
    {
        $this->factory = $factory;
        $this->menu = $menu;
    }

    /**
     * @return \Knp\Menu\FactoryInterface
     */
    public function getFactory()
    {
        return $this->factory;
    }

    /**
     * @return \Knp\Menu\ItemInterface
     */
    public function getMenu()
    {
        return $this->menu;
    }
}
```

**Note:** Following the Symfony2 best practices, the first segment of the
event name will be the alias of the bundle, which allows avoiding conflicts.

That's it. Your builder now provides a hook. Let's see how you can use it!

## Create a listener

You can register as many listeners as you want for the event. Let's add one.

```php
<?php
// src/Acme/OtherBundle/EventListener/ConfigureMenuListener.php

namespace Acme\OtherBundle\EventListener;

use Acme\DemoBundle\Event\ConfigureMenuEvent;

class ConfigureMenuListener
{
    /**
     * @param \Acme\DemoBundle\Event\ConfigureMenuEvent $event
     */
    public function onMenuConfigure(ConfigureMenuEvent $event)
    {
        $menu = $event->getMenu();

        $menu->addChild('Matches', array('route' => 'versus_rankedmatch_acp_matches_index'));
        $menu->addChild('Participants', array('route' => 'versus_rankedmatch_acp_participants_index'));
    }
}
```

You can now register the listener.

```yaml
services:
    acme_other.configure_menu_listener:
        class: Acme\OtherBundle\EventListener\ConfigureMenuListener
        tags:
          - { name: kernel.event_listener, event: acme_demo.menu_configure, method: onMenuConfigure }
```

**Note:** When using Symfony 2.1, you could also create your listener as
a subscriber and use the ``kernel.event_subscriber`` tag (which does not
have any additional attributes).
