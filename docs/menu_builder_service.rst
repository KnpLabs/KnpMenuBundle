Creating Menu Builders as Services
==================================

Start by creating a builder for your menu. You can stick as many menus into
a builder as you want, so you may only have one (or just a few) of these
builder classes in your application:

.. code-block:: php

    // src/Menu/MenuBuilder.php

    namespace App\Menu;

    use Knp\Menu\Attribute\AsMenuBuilder;
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

        #[AsMenuBuilder(name: 'main')] // The name is what is used to retrieve the menu
        public function createMainMenu(array $options): ItemInterface
        {
            $menu = $this->factory->createItem('root');

            $menu->addChild('Home', ['route' => 'homepage']);
            // ... add more children

            return $menu;
        }
    }

That's it! The menu is *very* configurable. For more details, see the
`KnpMenu documentation`_.

Next, register your menu builder as service and register its ``createMainMenu``
method as a menu builder. When using autoconfiguration, the ``#[AsMenuBuilder]``
attribute takes care of it. When not using autoconfiguration, you need to
register the menu builder by add the ``knp_menu.menu_builder`` tag:

.. code-block:: yaml

    # config/services.yaml
    services:
        app.menu_builder:
            class: App\Menu\MenuBuilder
            arguments: ["@knp_menu.factory"]
            tags:
                - { name: knp_menu.menu_builder, method: createMainMenu, alias: main } # The alias is what is used to retrieve the menu

        # ...

You can now render the menu directly in a template via the its name:

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

        #[AsMenuBuilder(name: 'sidebar')]
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

It can now be rendered, just like the other menu:

.. code-block:: html+jinja

    {% set menu = knp_menu_get('sidebar', [], {include_homepage: false}) %}
    {{ knp_menu_render(menu) }}

.. _`KnpMenu documentation`: https://github.com/KnpLabs/KnpMenu/blob/master/doc/01-Basic-Menus.md
