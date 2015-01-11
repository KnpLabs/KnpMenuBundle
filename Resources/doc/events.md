Using events to allow a menu to be extended
===========================================

To extend a menu, or change specific parts of it, you can just hook into the
`knp_menu.menu_configure`-event.

## Create a listener

You can register as many listeners as you want for the event. Let's add one.

```php
<?php
// src/Acme/OtherBundle/EventListener/ConfigureMenuListener.php

namespace Acme\OtherBundle\EventListener;

use Knp\Bundle\MenuBundle\Event\ConfigureMenuEvent;

class ConfigureMenuListener
{
    /**
     * @param \Knp\Bundle\MenuBundle\Event\ConfigureMenuEvent $event
     */
    public function onMenuConfigure(ConfigureMenuEvent $event)
    {
        // \Acme\DemoBundle\Menu\MainBuilder::build()
        if ($menu->getName() != 'AcmeDemoBundle:MainBuilder:build') {
            return;
        }

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
          - { name: kernel.event_listener, event: knp_menu.menu_configure, method: onMenuConfigure }
```

**Note:** When using Symfony 2.1, you could also create your listener as
a subscriber and use the ``kernel.event_subscriber`` tag (which does not
have any additional attributes).
