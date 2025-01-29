Creating Menu Builders as Services
==================================

This bundle gives you a really convenient way to create menus by following
a convention.

However, if you want to, you can instead choose to create a service for your
menu builder. The advantage of this method is that you can inject the exact
dependencies that your menu builder needs.
This can lead to code that is more testable and also potentially
more reusable. The disadvantage is that it needs just a little more setup.

Start by creating a builder for your menu. You can stick as many menus into
a builder as you want, so you may only have one (or just a few) of these
builder classes in your application:

.. code-block:: php

    // src/Menu/MenuBuilder.php

    namespace App\Menu;

    use Knp\Menu\FactoryInterface;
    use Knp\Menu\ItemInterface;

    class MenuBuilder
    {
        private $factory;

        /**
         * Add any other dependency you need...
         */
        public function __construct(FactoryInterface $factory)
        {
            $this->factory = $factory;
        }

        public function createMainMenu(array $options): ItemInterface
        {
            $menu = $this->factory->createItem('root');

            $menu->addChild('Home', ['route' => 'homepage']);
            // ... add more children

            return $menu;
        }
    }

Next, register your menu builder as service and register its ``createMainMenu`` method as a menu builder:

.. code-block:: yaml

    # config/services.yaml
    services:
        app.menu_builder:
            class: App\Menu\MenuBuilder
            arguments: ["@knp_menu.factory"]
            tags:
                - { name: knp_menu.menu_builder, method: createMainMenu, alias: main } # The alias is what is used to retrieve the menu

        # ...

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

        public function createSidebarMenu(array $options): ItemInterface
        {
            $menu = $this->factory->createItem('sidebar');

            if (isset($options['include_homepage']) && $options['include_homepage']) {
                $menu->addChild('Home', ['route' => 'homepage']);
            }

            // ... add more children

            return $menu;
        }
    }

Now, create a service for *just* your new menu, giving it a new name, like
``sidebar``:

.. code-block:: yaml

    # config/services.yaml
    services:
        app.menu_builder:
            class: App\Menu\MenuBuilder
            arguments: ["@knp_menu.factory"]
            tags:
                - { name: knp_menu.menu_builder, method: createMainMenu, alias: main } # the previous menu
                - { name: knp_menu.menu_builder, method: createSidebarMenu, alias: sidebar } # Named "sidebar" this time

        # ...

It can now be rendered, just like the other menu:

.. code-block:: html+jinja

    {% set menu = knp_menu_get('sidebar', [], {include_homepage: false}) %}
    {{ knp_menu_render(menu) }}
