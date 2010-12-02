MenuBundle
==========

The MenuBundle provides object oriented menus for your Symfony2 project.
The core menu objects can even be used outside of Symfony2:

    use Bundle\MenuBundle\MenuItem;

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

To install the bundle, place it in the `src/Bundle` directory of your project
(so that it lives at `src/Bundle/MenuBundle`). You can do this by adding
the bundle as a submodule, cloning it, or simply downloading the source.

### Initializing the bundle

To start using the bundle, initialize the bundle in your Kernel. This
file is usually located at `app/AppKernel`:

    public function registerBundles()
    {
        $bundles = array(
            // ...
            new Bundle\MenuBundle\MenuBundle(),
        );
    )

That's it! Other than a few templating helpers (explained next), the `MenuBundle`
is a standalone PHP 5.3 library and can be used as soon as Symfony2's
class autoloader is aware of it (this was just accomplished above).

The MenuBundle also offers two templating helpers that assist you in rendering
your menus in the view. Two of them are available - one for PHP templates
and another for Twig templates. For more information on these helpers,
see the extended documentation in the `Resources/doc` directory.

To enable the templating helpers, add the following to your `config.yml`
or `config.xml` file (depending on which you're using in your project).
For `config.yml`:

    # to enable the twig view helper
    menu.twig:   ~

    # to enable the PHP view helper
    menu.templating: ~

and for `config.xml`:

    <!-- to enable the twig view helper -->
    <menu:twig />

    <!-- to enable the PHP view helper -->
    <menu:templating />

When the `MenuBundle` sees the above configuration, it will load the
appropriate view helpers on your behalf.

## Credits

This bundle was originally ported from [ioMenuPlugin](http://github.com/weaverryan/ioMenuPlugin),
a menu plugin for symfony1. It has since been developed by knpLabs and
the Symfony community.
