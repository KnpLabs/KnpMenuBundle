Using events to allow a menu to be extended
===========================================

If you want to let different parts of your system hook into the building of your
menu, a good way is to use an approach based on the Symfony EventDispatcher
component.

Create the menu builder
-----------------------

Your menu builder will create the base menu item and then dispatch an event
to allow other parts of your application to add more stuff to it.

.. code-block:: php

    // src/Menu/MainBuilder.php

    namespace App\Menu;

    use App\Event\ConfigureMenuEvent;
    use Knp\Menu\FactoryInterface;
    use Symfony\Contracts\EventDispatcher\EventDispatcherInterface:

    class MainBuilder
    {
        private $factory;
        private $eventDispatcher

        public function __construct(FactoryInterface $factory, EventDispatcherInterface $eventDispatcher)
        {
            $this->factory = $factory;
            $this->eventDispatcher = $eventDispatcher;
        }

        public function build(array $options)
        {
            $menu = $this->factory->createItem('root');

            $menu->addChild('Dashboard', ['route' => '_acp_dashboard']);

            $this->eventDispatcher->dispatch(
                new ConfigureMenuEvent($this->factory, $menu),
                ConfigureMenuEvent::CONFIGURE
            );

            return $menu;
        }
    }

Create the Event object
-----------------------

The event object allows passing some data to the listener. In this case,
it will hold the menu being created and the factory.

.. code-block:: php

    // src/Event/ConfigureMenuEvent.php

    namespace App\Event;

    use Knp\Menu\FactoryInterface;
    use Knp\Menu\ItemInterface;
    use Symfony\Component\EventDispatcher\Event;

    class ConfigureMenuEvent extends Event
    {
        const CONFIGURE = 'app.menu_configure';

        private $factory;
        private $menu;

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

That's it. Your builder now provides a hook. Let's see how you can use it!

Create a listener
-----------------

You can register as many listeners as you want for the event. Let's add one.

.. code-block:: php

    // src/Acme/AdminBundle/EventListener/ConfigureMenuListener.php

    namespace Acme\AdminBundle\EventListener;

    use App\Event\ConfigureMenuEvent;

    class ConfigureMenuListener
    {
        public function __invoke(ConfigureMenuEvent $event)
        {
            $menu = $event->getMenu();

            $menu->addChild('Matches', ['route' => 'versus_rankedmatch_acp_matches_index']);
            $menu->addChild('Participants', ['route' => 'versus_rankedmatch_acp_participants_index']);
        }
    }

You can now register the listener.

.. code-block:: yaml

    # config/services.yaml
    services:
        app.admin_configure_menu_listener:
            class: Acme\AdminBundle\EventListener\ConfigureMenuListener
            tags: [kernel.event_listener]


You could also create your listener as a subscriber and use the ``kernel.event_subscriber``
tag, which does not have any additional attributes.
