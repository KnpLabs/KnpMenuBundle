Creating Menus as Services
==========================

This bundle gives you a really convenient way to create menus by following
a convention and - if needed - injecting the entire container.

However, if you want to, you can instead choose to create a service for your
menu object. The advantage of this method is that you can inject the exact
dependencies that your menu needs, instead of injecting the entire service
container. This can lead to code that is more testable and also potentially
more reusable. The disadvantage is that it needs just a little more setup.

Start by creating a builder for your menu. You can stick as many menus into
a builder as you want, so you may only have one (or just a few) of these
builder classes in your application:

.. code-block:: php

    // src/AppBundle/Menu/MenuBuilder.php

    namespace AppBundle\Menu;

    use Knp\Menu\FactoryInterface;
    use Symfony\Component\HttpFoundation\RequestStack;

    class MenuBuilder
    {
        private $factory;

        /**
         * @param FactoryInterface $factory
         */
        public function __construct(FactoryInterface $factory)
        {
            $this->factory = $factory;
        }

        public function createMainMenu(RequestStack $requestStack)
        {
            $menu = $this->factory->createItem('root');

            $menu->addChild('Home', array('route' => 'homepage'));
            // ... add more children

            return $menu;
        }
    }

Next, register two services: one for your menu builder, and one for the menu
object created by the ``createMainMenu`` method:

.. code-block:: yaml

    # app/config/services.yml
    services:
        app.menu_builder:
            class: AppBundle\Menu\MenuBuilder
            arguments: ["@knp_menu.factory"]

        app.main_menu:
            class: Knp\Menu\MenuItem # the service definition requires setting the class
            factory: ["@app.menu_builder", createMainMenu]
            arguments: ["@request_stack"]
            tags:
                - { name: knp_menu.menu, alias: main } # The alias is what is used to retrieve the menu

        # ...

.. note::

    The menu service must be public as it will be retrieved at runtime to keep
    it lazy-loaded.

.. note::

    If you are using Symfony `2.5` or older version please check the `Using a Factory to Create Services`_
    article for correct factories syntax corresponding to your version.

You can now render the menu directly in a template via the name given in the
``alias`` key above:

.. code-block:: html+jinja

    {{ knp_menu_render('main') }}

Suppose now we need to create a second menu for the sidebar. The process
is simple! Start by adding a new method to your builder:

.. code-block:: php

    // src/AppBundle/Menu/MenuBuilder.php

    // ...

    class MenuBuilder
    {
        // ...

        public function createSidebarMenu(RequestStack $requestStack)
        {
            $menu = $this->factory->createItem('sidebar');

            $menu->addChild('Home', array('route' => 'homepage'));
            // ... add more children

            return $menu;
        }
    }

Now, create a service for *just* your new menu, giving it a new name, like
``sidebar``:

.. code-block:: yaml

    # app/config/services.yml
    services:
        app.sidebar_menu:
            class: Knp\Menu\MenuItem
            factory: ["@app.menu_builder", createSidebarMenu]
            arguments: ["@request_stack"]
            tags:
                - { name: knp_menu.menu, alias: sidebar } # Named "sidebar" this time

        # ...

It can now be rendered, just like the other menu:

.. code-block:: html+jinja

    {{ knp_menu_render('sidebar') }}

Disabling the core menu providers
---------------------------------

To be able to use different menu providers together (the container-based
one and the builder-based one for instance), a chain provider is used.
However, it is not used when only one provider is enabled to increase performance
by getting rid of the wrapping. If you don't want to use the built-in providers,
you can disable them through the configuration:

.. code-block:: yaml

    #app/config/config.yml
    knp_menu:
        providers:
            builder_alias: false    # disable the builder-based provider
            container_aware: true   # keep this one enabled. Can be omitted as it is the default

.. note::

    Both providers are enabled by default.

.. _`Using a Factory to Create Services`: http://symfony.com/doc/2.5/components/dependency_injection/factories.html
