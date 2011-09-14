Upgrade Instructions
====================

From pre 1.0 to now
-------------------

If you worked with this bundle before **Aug 30th, 2011**, then a number of
changes have occurred to the bundle. To handle the changes, you have 2 options:

**1) I'm cool, I don't really need to upgrade**

No problem. You can lock KnpMenuBundle at the old version by adding the following
to your `deps.lock` file:

    KnpMenuBundle e5d9c865ea7375abd6dbd251d47fc016597053ac

When you run `bin/vendor install`, the original version of the bundle will
be downloaded

**2) Let's upgrade!**

The bundle has undergone a number of fundamental changes. We'll walk you
through each one.

### a) New setup

To start, you'll need to add a new item to your `deps` file:

```
[knp-menu]
    git=https://github.com/knplabs/KnpMenu.git
```

Next, make sure the `Knp` entries in your `app/autoload.php` file look like
this:

``` php
<?php
// app/autoload.php
$loader->registerNamespaces(array(
    // ...
    'Knp\Bundle' => __DIR__.'/../vendor/bundles',
    'Knp\Menu'   => __DIR__.'/../vendor/knp-menu/src',
    // ...
));
```

### b) Working with menus

Previously, a menu was just a class that you could instantiate. Now, you
should use a menu factory to create menus. There have also been a few other
changes in the syntax for reating menus. From inside a controller:

**Before**:

``` php
$menu = new MenuItem('My menu');
$menu->addChild('Home', $this->generateUrl('homepage'));
$menu->addChild('Comments', $this->generateUrl('about'));
```

**After**:

``` php

$menuFactory = $this->get('knp_menu.factory');

$menu = $menuFactory->createItem('My menu');
$menu->addChild('Home', array('uri' => $this->generateUrl('homepage')));
$menu->addChild('Comments', array('uri' => $this->generateUrl('about')));
```

### c) Menu classes

Previously, you could create a menu class, make it a service, tag it with
`knp_menu.menu`, and then render it in a Twig template. This is still true,
except that instead of having many menu classes, you have one menu "builder",
which can create many menus.

**Before**:

``` php
<?php // src/MyVendor/MyBundle/Menu/MainMenu.php

namespace Acme\MainBundle\Menu;

use Knp\Bundle\MenuBundle\Menu;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Router;

class MainMenu extends Menu
{
    /**
     * @param Request $request
     * @param Router $router
     */
    public function __construct(Request $request, Router $router)
    {
        parent::__construct();

        $this->setCurrentUri($request->getRequestUri());

        $this->addChild('Home', $router->generate('homepage'));
        // ... add more children
    }
}
```

**After**:

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
     * @param \Knp\Menu\FactoryInterface $factory
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

You'll still create a service for your "main" menu, but now, this will look
a little bit different.

**Before**:

```php
services:
    acme_main.menu_main:
        class:   Acme\MainBundle\Menu\MainMenu
        tags:
            -    { name: knp_menu.menu, alias: main }
        arguments:
            -    @request
            -    @router
```

**After**:

```yaml
services:
    # you'll just need this 
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

Finally, rendering the menu in a template is a little bit different:

**Before**:

```jinja
{{ {{ knp_menu('main') }} }}
```

**After**:

```jinja
{{ knp_menu_render('main') }}
```