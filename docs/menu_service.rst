Creating Menus as Services
==========================

.. note::

    Registering a menu as service comes with several limitations:

    - it does not allow to use builder options
    - it reuses the same instance several times in case you render the same
      menu several times, which can have weird side-effects.

    It is recommended to register only :doc:`menu builders as services <menu_builder_service>`
    instead.

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

    // src/Menu/MenuBuilder.php

    namespace App\Menu;

    use Knp\Menu\FactoryInterface;
    use Symfony\Component\HttpFoundation\RequestStack;

    class MenuBuilder
    {
        private $factory;

        public function __construct(FactoryInterface $factory)
        {
            $this->factory = $factory;
        }

        public function createMainMenu(RequestStack $requestStack)
        {
            $menu = $this->factory->createItem('root');

            $menu->addChild('Home', ['route' => 'homepage']);
            // ... add more children

            return $menu;
        }
    }

Next, register two services: one for your menu builder, and one for the menu
object created by the ``createMainMenu`` method:

.. code-block:: yaml

    # config/services.yaml
    services:
        app.menu_builder:
            class: App\Menu\MenuBuilder
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

You can now render the menu directly in a template via the name given in the
``alias`` key above:

.. code-block:: html+jinja

    {{ knp_menu_render('main') }}

Suppose now we need to create a second menu for the sidebar. The process
is simple! Start by adding a new method to your builder:

.. code-block:: php

    // src/Menu/MenuBuilder.php

    // ...

    class MenuBuilder
    {
        // ...

        public function createSidebarMenu(RequestStack $requestStack)
        {
            $menu = $this->factory->createItem('sidebar');

            $menu->addChild('Home', ['route' => 'homepage']);
            // ... add more children

            return $menu;
        }
    }

Now, create a service for *just* your new menu, giving it a new name, like
``sidebar``:

.. code-block:: yaml

    # config/services.yaml
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
