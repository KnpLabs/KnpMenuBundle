Creating Menus as Services
==========================

This bundle gives you a really convenient way to create menus by following
a convention and - if needed - injected the entire container. A more flexible
or precise method of creating a menu is to register it as a service.

Start by creating a builder for your menu. You can stick as many menus into
a builder as you want, so you may only have one of these builder classes
in your application:

```php
<?php
// src/Acme/MainBundle/Menu/MenuBuilder.php

namespace Acme\MainBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\HttpFoundation\Request;

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

    public function createMainMenu(Request $request)
    {
        $menu = $this->factory->createItem('root');
        $menu->setCurrentUri($request->getRequestUri());

        $menu->addChild('Home', array('route' => 'homepage'));
        // ... add more children

        return $menu;
    }
}
```

Next, register two services: one for your menu builder, and one for the menu
object created by the `createMainMenu` method:

```yaml
# src/Acme/MainBundle/Resources/config/services.yml
services:
    acme_main.menu_builder:
        class: Acme\MainBundle\Menu\MenuBuilder
        arguments: ["@knp_menu.factory", "@router"]

    acme_main.menu.main:
        class: Knp\Menu\MenuItem # the service definition requires setting the class
        factory_service: acme_hello.menu_builder
        factory_method: createMainMenu
        arguments: ["@request"]
        scope: request # needed as we have the request as a dependency here
        tags:
            - { name: knp_menu.menu, alias: main } # The alias is what is used to retrieve the menu
```

>**NOTE**
>The menu service must be public as it will be retrieved at runtime to keep
>it lazy-loaded.

You can now retrieve the menu by its name in your template:

You can now render the menu directly in a template via the name given in the
`alias` key above:

```jinja
{{ knp_menu_render('main') }}
```

Suppose now we need to create a second menu for the sidebar. The process
is simple! Start by adding a new method to your builder:

```php
<?php
// src/Acme/MainBundle/Menu/MenuBuilder.php

// ...

class MenuBuilder
{
    // ...

    public function createSidebarMenu(Request $request)
    {
        $menu = $this->factory->createItem('sidebar');
        $menu->setCurrentUri($request->getRequestUri());

        $menu->addChild('Home', array('route' => 'homepage'));
        // ... add more children

        return $menu;
    }
}
```

Now, create a service for *just* your new menu, giving it a new name, like
`sidebar`:

```yaml
# src/Acme/MainBundle/Resources/config/services.yml
services:

    acme_main.menu.sidebar:
        class: Knp\Menu\MenuItem
        factory_service: acme_hello.menu_builder
        factory_method: createSidebarMenu
        arguments: ["@request"]
        scope: request
        tags:
            - { name: knp_menu.menu, alias: sidebar } # Named "sidebar" this time
```

It can now be rendered, just like the other menu:

```jinja
{{ knp_menu_render('sidebar') }}
```