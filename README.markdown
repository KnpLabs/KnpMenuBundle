MenuBundle
==========

The MenuBundle provides object oriented menus for your Symfony2 project.
The core menu objects can even be used outside of Symfony2:

    use Knplabs\MenuBundle\MenuItem;

    $menu = new MenuItem('My menu');
    $menu->addChild('Home', $router->generate('homepage'));
    $menu->addChild('Comments', $router->generate('comments'));
    $menu->addChild('Symfony2', 'http://symfony-reloaded.org/');
    echo $menu->render();

The above menu would render the following HTML:

    <ul>
      <li class="first">
        <a href="/">Home</a>
      </li>
      <li class="current">
        <a href="/comments">Comments</a>
      </li>
      <li class="last">
        <a href="http://symfony-reloaded.org/">Symfony2</a>
      </li>
    </ul>

## Reference Manual

The bulk of the documentation can be found in the `Resources/doc` directory.

## Installation

### Get the bundle

To install the bundle, place it in the `src/Knplabs` directory of your project
(so that it lives at `src/Knplabs/MenuBundle`). You can do this by adding
the bundle as a submodule, cloning it, or simply downloading the source.

    git submodule add https://github.com/knplabs/MenuBundle.git src/Knplabs/MenuBundle

### Add the namespace to your autoloader

If it is the first Knplabs bundle you install in your Symfony 2 project, you
need to add the `Knplabs` namespace to your autoloader:

    // app/autoload.php
    $loader->registerNamespaces(array(
        'Knplabs'                       => __DIR__.'/../src'
        // ...
    ));

### Initializing the bundle

To start using the bundle, initialize the bundle in your Kernel. This
file is usually located at `app/AppKernel`:

    public function registerBundles()
    {
        $bundles = array(
            // ...
            new Knplabs\MenuBundle\KnplabsMenuBundle(),
        );
    )

That's it! Other than a few templating helpers (explained next), the `MenuBundle`
is a standalone PHP 5.3 library and can be used as soon as Symfony2's
class autoloader is aware of it (this was just accomplished above).

### Templating helper

The MenuBundle also offers templating helper that assist you in rendering
your menus in the view. Two of them are available - one for PHP templates
and another for Twig templates. For more information on these helpers,
see the extended documentation in the `Resources/doc` directory.

To enable the templating helpers, add the following to your `config.yml`
or `config.xml` file (depending on which you're using in your project).
For `config.yml`:

    # to enable the twig view helper
    knplabs_menu:
        twig: true

    # to enable the PHP view helper
    knplabs_menu:
        templating: true

and for `config.xml`:

    <!-- to enable the twig view helper -->
    <knplabs_menu:twig />

    <!-- to enable the PHP view helper -->
    <knplabs_menu:templating />

When the `MenuBundle` sees the above configuration, it will load the
appropriate view helpers on your behalf.

Ensure that the **php** engine is enabled in your config:

    framework:
        templating: { engines: ['twig', 'php'] } # twig is optional

## Credits

This bundle was originally ported from [ioMenuPlugin](http://github.com/weaverryan/ioMenuPlugin),
a menu plugin for symfony1. It has since been developed by [knpLabs](http://www.knplabs.com) and
the Symfony community.
