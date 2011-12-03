Upgrade Instructions
====================

From pre 1.0 to now
-------------------

If you worked with this bundle before **Aug 30th, 2011**, then a number of
changes have occurred to the bundle. To handle the changes, you have 2 options:

**1) I'm cool, I don't really need to upgrade**

No problem. The original version of the bundle was tagged and called `legacy`.
You can lock KnpMenuBundle at the legacy version by adding a `version` option
to the `[KnpMenuBundle]` entry of your `deps` file

```
[KnpMenuBundle]
    git=https://github.com/KnpLabs/KnpMenuBundle.git
    target=bundles/Knp/Bundle/MenuBundle
    version=legacy
```

When you run `php bin/vendors install`, the original version of the bundle will
be downloaded.

**2) Let's upgrade!**

The bundle has undergone a number of fundamental changes. We'll walk you
through each one.

### a) New setup

To start, you'll need to add a new item to your `deps` file:

```
[knp-menu]
    git=https://github.com/KnpLabs/KnpMenu.git
```

Next, make sure the `Knp` entries in your `app/autoload.php` file look like
this:

```php
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
changes in the syntax for creating menus. From inside a controller:

**Before**:

```php
$menu = new MenuItem('My menu');
$menu->addChild('Home', $this->generateUrl('homepage'));
$menu->addChild('Comments', $this->generateUrl('comments'));
```

**After**:

```php
$menuFactory = $this->get('knp_menu.factory');

$menu = $menuFactory->createItem('My menu');
$menu->addChild('Home', array('uri' => $this->generateUrl('homepage')));
$menu->addChild('Comments', array('uri' => $this->generateUrl('comments')));
```

### c) Menu classes

Previously, you could create a menu class, make it a service, tag it with
`knp_menu.menu`, and then render it in a Twig template. This is still possible,
except that you would need to inject the `knp_menu.factory` service into
your new menu. The new menu class might look something like this:

```php
<?php
namespace Acme\MainBundle\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\MenuItem;

class MainMenu extends MenuItem
{
    public function __construct(FactoryInterface $factory)
    {
        parent::__construct('Main Menu', $factory);

        $this->addChild('Comments', array('route' => 'comments'));
        // ...
    }
}
```

A better way, however, might be to have just one class - called a menu "builder" -
which is responsible for creating as many different menus as you want. For
example, a single `MenuBuilder` might have `createMainMenu` and `createSidebarMenu`
methods, each which create a different menu.

Let's look at how using a menu builder differs from the old approach of

**Before**:

```php
<?php
// src/Acme/MainBundle/Menu/MainMenu.php

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

You'll still create a service for your "main" menu, but now, this will look
a little bit different.

**Before**:

```yaml
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
        arguments: ["@knp_menu.factory"]

    acme_main.menu.main:
        class: Knp\Menu\MenuItem # the service definition requires setting the class
        factory_service: acme_hello.menu_builder
        factory_method: createMainMenu
        arguments: ["@request"]
        scope: request # needed as we have the request as a dependency here
        tags:
            - { name: knp_menu.menu, alias: main } # The alias is what is used to retrieve the menu
```

The key difference is that the single builder itself needs to be registered
as a service. Then, a tagged menu service is created for each method on the
builder (e.g. `createMainMenu`) that creates a menu.

Finally, rendering the menu in a template is a little bit different:

**Before**:

```jinja
{{ knp_menu('main') }}
```

**After**:

```jinja
{{ knp_menu_render('main') }}
```