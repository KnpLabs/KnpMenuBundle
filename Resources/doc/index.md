Using KnpMenuBundle
===================

Welcome to KnpMenuBundle - creating menus is fun again!

**Basic Docs**

* [Installation](#installation)
* [Your first menu](#first-menu)
* [Rendering Menus](#rendering-menus)
* [Using PHP Templates](#php-templates)

**More Advanced Stuff**

* [Menus as Services](menu_service.md)
* [Custom Menu Renderer](custom_renderer.md)
* [I18n for your menu labels](i18n.md)
* [Using events to allow extending the menu](events.md)

<a name="installation"></a>

## Installation

### Step 1) Get the bundle and the library

First, grab the KnpMenu library and KnpMenuBundle. There are two different ways
to do this:

#### Method a) Using the `deps` file

Add the following lines to your  `deps` file and then run `php bin/vendors
install`:

```
[KnpMenu]
    git=https://github.com/KnpLabs/KnpMenu.git

[KnpMenuBundle]
    git=https://github.com/KnpLabs/KnpMenuBundle.git
    target=bundles/Knp/Bundle/MenuBundle
```

#### Method b) Using submodules

Run the following commands to bring in the needed libraries as submodules.

```bash
git submodule add https://github.com/KnpLabs/KnpMenuBundle.git vendor/bundles/Knp/Bundle/MenuBundle
git submodule add https://github.com/KnpLabs/KnpMenu.git vendor/KnpMenu
```

### Step 2) Register the namespaces

Add the following two namespace entries to the `registerNamespaces` call
in your autoloader:

``` php
<?php
// app/autoload.php
$loader->registerNamespaces(array(
    // ...
    'Knp\Bundle' => __DIR__.'/../vendor/bundles',
    'Knp\Menu'   => __DIR__.'/../vendor/KnpMenu/src',
    // ...
));
```

### Step 3) Register the bundle

To start using the bundle, register it in your Kernel:

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Knp\Bundle\MenuBundle\KnpMenuBundle(),
    );
    // ...
}
```

### Step 4) (optional) Configure the bundle

The bundle comes with a sensible default configuration, which is listed below.
If you skip this step, these defaults will be used.

```yaml
# app/config/config.yml
knp_menu:
    twig:  # use "twig: false" to disable the Twig extension and the TwigRenderer
        template: knp_menu.html.twig
    templating: false # if true, enables the helper for PHP templates
    default_renderer: twig # The renderer to use, list is also available by default
```

**Note:** Take care to change the default renderer if you disable the Twig support.

<a name="first-menu"></a>

## Create your first menu!

There are two ways to create a menu: the "easy" way, and the more flexible
method of creating a menu as a service.

### Method a) The Easy Way (yay)!

To create a menu, first create a new class in the `Menu` directory of one
of your bundles. This class - called `Builder` in our example - will have
one method for each menu that you need to build.

An example builder class would look like this:

```php
<?php
// src/Acme/DemoBundle/Menu/Builder.php
namespace Acme\DemoBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAware;

class Builder extends ContainerAware
{
    public function mainMenu(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('root');
        $menu->setCurrentUri($this->container->get('request')->getRequestUri());

        $menu->addChild('Home', array('route' => 'homepage'));
        $menu->addChild('About Me', array(
            'route' => 'page_show',
            'routeParameters' => array('id' => 42)
        ));
        // ... add more children

        return $menu;
    }
}
```

**Note** You only need to extend `ContainerAware` if you need the service
container to be available via `$this->container`. You can also implement
`ContainerAwareInterface` instead of extending this class.

**Note** The menu builder can be overwritten using the bundle inheritance.

To actually render the menu, just do the following from anywhere in any Twig
template:

```jinja
{{ knp_menu_render('AcmeDemoBundle:Builder:mainMenu') }}
```

With this method, you refer to the menu using a three-part string:
**bundle**:**class**:**method**.

If you needed to create a second menu, you'd simply add another method to
the `Builder` class (e.g. `sidebarMenu`), build and return the new menu,
then render it via `AcmeDemoBundle:Builder:sidebarMenu`.

That's it! The menu is *very* configurable. For more details, see the
[KnpMenu](https://github.com/KnpLabs/KnpMenu/blob/master/doc/01-Basic-Menus.markdown)
documentation.

### Method b) A menu as a service

For information on how to register a service and tag it as a menu, read
[Creating Menus as Services](https://github.com/KnpLabs/KnpMenuBundle/blob/master/Resources/doc/menu_service.md).

<a name="rendering-menus"></a>

## Rendering Menus

Once you've setup your menu, rendering it easy. If you've used the "easy"
way, then do the following:

```jinja
{{ knp_menu_render('AcmeDemoBundle:Builder:mainMenu') }}
```

Additionally, you can pass some options to the renderer:

```jinja
{{ knp_menu_render('AcmeDemoBundle:Builder:mainMenu', {'depth': 2, 'currentAsLink': false}) }}
```

For a full list of options, see the "Other rendering options" header on the
[KnpMenu](https://github.com/KnpLabs/KnpMenu/blob/master/doc/01-Basic-Menus.markdown) documentation.

You can also "get" a menu, which you can use to render later:

```jinja
{% set menuItem = knp_menu_get('AcmeDemoBundle:Builder:mainMenu') %}

{{ knp_menu_render(menuItem) }}
```

If you want to only retrieve a certain branch of the menu, you can do the
following, where 'Contact' is one of the root menu items and has children
beneath it.

```jinja
{% set menuItem = knp_menu_get('AcmeDemoBundle:Builder:mainMenu', ['Contact']) %}

{{ knp_menu_render(['AcmeDemoBundle:Builder:mainMenu', 'Contact']) }}
```

If you want to pass some options to the builder, you can use the third parameter
of the `knp_menu_get` function:

```jinja
{% set menuItem = knp_menu_get('AcmeDemoBundle:Builder:mainMenu', [], {'some_option': 'my_value'}) %}

{{ knp_menu_render(menuItem) }}
```

<a name="php-templates"></a>

## Using PHP templates

If you prefer using PHP templates, you can use the templating helper to render
and retrieve your menu from a template, just like available in Twig.

```php
// Retrieves an item by its path in the main menu
$item = $view['knp_menu']->get('AcmeDemoBundle:Builder:main', array('child'));

// Render an item
echo $view['knp_menu']->render($item, array(), 'list');
```
