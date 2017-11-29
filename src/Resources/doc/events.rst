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

  // src/AppBundle/Menu/MainBuilder.php

  namespace AppBundle\Menu;

  use AppBundle\Event\ConfigureMenuEvent;
  use Knp\Menu\FactoryInterface;
  use Symfony\Component\DependencyInjection\ContainerAwareInterface;
  use Symfony\Component\DependencyInjection\ContainerAwareTrait;

  class MainBuilder implements ContainerAwareInterface
  {
      use ContainerAwareTrait;
      
      public function build(FactoryInterface $factory)
      {
          $menu = $factory->createItem('root');

          $menu->addChild('Dashboard', array('route' => '_acp_dashboard'));

          $this->container->get('event_dispatcher')->dispatch(
              ConfigureMenuEvent::CONFIGURE,
              new ConfigureMenuEvent($factory, $menu)
          );

          return $menu;
      }
  }

.. note::

  This implementation assumes you use the ``BuilderAliasProvider`` (getting
  your menu as ``AppBundle:MainBuilder:build``) but you could also define
  it as a service and inject the ``event_dispatcher`` service as a dependency.

Create the Event object
-----------------------

The event object allows to pass some data to the listener. In this case,
it will hold the menu being created and the factory.

.. code-block:: php

    // src/AppBundle/Event/ConfigureMenuEvent.php

    namespace AppBundle\Event;

    use Knp\Menu\FactoryInterface;
    use Knp\Menu\ItemInterface;
    use Symfony\Component\EventDispatcher\Event;

    class ConfigureMenuEvent extends Event
    {
        const CONFIGURE = 'app.menu_configure';

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

.. note::

  Following the Symfony best practices, the first segment of the event name will
  be the alias of the bundle, which allows avoiding conflicts.

That's it. Your builder now provides a hook. Let's see how you can use it!

Create a listener
-----------------

You can register as many listeners as you want for the event. Let's add one.

.. code-block:: php

    // src/Acme/AdminBundle/EventListener/ConfigureMenuListener.php

    namespace Acme\AdminBundle\EventListener;

    use AppBundle\Event\ConfigureMenuEvent;

    class ConfigureMenuListener
    {
        /**
         * @param \AppBundle\Event\ConfigureMenuEvent $event
         */
        public function onMenuConfigure(ConfigureMenuEvent $event)
        {
            $menu = $event->getMenu();

            $menu->addChild('Matches', array('route' => 'versus_rankedmatch_acp_matches_index'));
            $menu->addChild('Participants', array('route' => 'versus_rankedmatch_acp_participants_index'));
        }
    }

You can now register the listener.

.. code-block:: yaml

    # app/config/services.yml
    services:
        app.admin_configure_menu_listener:
            class: Acme\AdminBundle\EventListener\ConfigureMenuListener
            tags:
              - { name: kernel.event_listener, event: app.menu_configure, method: onMenuConfigure }


You could also create your listener as a subscriber and use the ``kernel.event_subscriber``
tag, which does not have any additional attributes.
