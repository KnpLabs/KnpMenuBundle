Using KnpMenuBundle
===================

## Installation

### Get the bundle and the library

To install the bundle, place it in the `vendor/bundles/Knp/Bundle` directory
of your project (so that it lives at `vendor/bundles/Knp/Bundle/MenuBundle`)
and place `KnpMenu` in your `vendor` folder.
You can do this by adding the bundle as a submodule, cloning it, or simply
downloading the source.

#### Using submodules

Simply run the following commands:

```bash
git submodule add https://github.com/knplabs/KnpMenuBundle.git vendor/bundles/Knp/Bundle/MenuBundle
git submodule add https://github.com/knplabs/KnpMenu.git vendor/knp-menu
```

#### Using the `deps` file

You can also achieve the same by using the `deps` file. Simply add the new
vendors in the file and run ``php bin/vendors install``:

```
[knp-menu]
    git=https://github.com/knplabs/KnpMenu.git
[KnpMenuBundle]
    git=https://github.com/knplabs/KnpMenuBundle.git
    target=bundles/Knp/Bundle/MenuBundle
```

### Add the namespaces to your autoloader

    // app/autoload.php
    $loader->registerNamespaces(array(
        'Knp\Bundle' => __DIR__.'/../vendor/bundles',
        'Knp\Menu'   => __DIR__.'/../vendor/knp-menu/src',
        // ...
    ));

### Register the bundle

To start using the bundle, register it in your Kernel. This file is usually
located at `app/AppKernel`:

    public function registerBundles()
    {
        $bundles = array(
            // ...
            new Knp\Bundle\MenuBundle\KnpMenuBundle(),
        );
    )

### Configure the bundle

```yaml
# app/config/config.yml
knp_menu:
    twig: false  # disables the Twig extension and the TwigRenderer
    templating: true # enables the helper for PHP templates
    default_renderer: list # Change the default renderer as we disabled the Twig one
    template: AcmeFooBundle:Menu:knp_menu.html.twig # override the deafult template
```

>**NOTE**
>The configuration is optional. If you omit it, the default behavior is to
>enable the Twig support, to disable the PHP helper (as Twig is the recommended
>templating engine in Symfony2), use the Twig renderer as default renderer and to use default template placed in the 'KnpMenu'.

## Create a menu

To create a menu, simply follow the way described in the `KnpMenu` doc.

>**NOTE**
>The `RouterAwareFactory` is available as the `knp_menu.factory` service.

## Registering a menu in the provider

Registering a menu in the MenuProvider (to access it by its name in the templates)
is simply a matter of creating a service and tagging it with the `knp_menu.menu`
tag.

>**NOTE**
>Registering your menu in the menu provider is optional. You could also create
>it the menu in your controller and pass it explicitly to your template.

A good way to build the menu tree is to create a builder and use it as factory
service for the menu.

Create a builder for your menu:

```php
<?php
// src/Acme/HelloBundle/Menu/MenuBuilder.php

namespace Acme\HelloBundle\Menu;

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

Then register your services:

```yaml
# src/Acme/HelloBundle/Resources/config/services.yml
services:
    acme_hello.menu_builder:
        class: Acme\HelloBundle\Menu\MenuBuilder
        arguments: ["@knp_menu.factory"]

    acme_hello.menu.main:
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

```jinja
{{ knp_menu_render('main') }}
```

## Registering your own renderer

Registering your own renderer in the renderer provider is simply a matter
of creating a service tagger with `knp_menu.renderer`:

```yaml
# src/Acme/HelloBundle/Resources/config/services.yml
services:
    acme_hello.menu_renderer:
        class: Acme\HelloBundle\Menu\CustomRenderer # The class implements Knp\Menu\Renderer\RendererInterface
        arguments: [%kernel.charset%] # set your own dependencies here
        tags:
            - { name: knp_menu.renderer, alias: custom } # The alias is what is used to retrieve the menu
```

>**Note**
>The renderer service must be public as it will be retrieved at runtime to
>keep it lazy-loaded.

You can now use your renderer to render your menu:

```jinja
{{ knp_menu_render('main', {'my_custom_option': 'some_value'}, 'custom') }}
```

>**NOTE**
>As the renderer is responsible to render some HTML code, the `knp_menu_render`
>filter is marked as safe. Take care to handle escaping data in your renderer
>to avoid XSS if you use some user input in the menu.

## Using PHP templates

If you prefer using PHP templates, you can use the templating helper to render
and retrieve your menu from a template, just like available in Twig.

```php
// Retrieves an item by its path in the main menu
$item = $view['knp_menu']->get('main', array('child'));

// Render an item
echo $view['knp_menu']->render($item, array(), 'list');
```
