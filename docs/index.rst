Using KnpMenuBundle
===================

Welcome to KnpMenuBundle - creating menus is fun again!

Installation
------------

Step 1: Download the Bundle
~~~~~~~~~~~~~~~~~~~~~~~~~~~

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

.. code-block:: bash

    composer require knplabs/knp-menu-bundle

This command requires you to have Composer installed globally, as explained
in the `installation chapter`_ of the Composer documentation.

Step 2: Enable the Bundle
~~~~~~~~~~~~~~~~~~~~~~~~~

KnpMenuBundle should be automatically enabled and configured, thanks to `Flex`_.

If you don't use Flex, you can manually enable it, by adding the following line in
your project's Kernel:

.. code-block:: php

    // e.g. app/AppKernel.php

    // ...
    class AppKernel extends Kernel
    {
        public function registerBundles()
        {
            $bundles = [
                // ...

                new Knp\Bundle\MenuBundle\KnpMenuBundle(),
            ];

            // ...
        }

        // ...
    }

Step 3: (optional) Configure the bundle
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

The bundle comes with a sensible default configuration, which is listed below.
You can define these options if you need to change them:

.. configuration-block::

    .. code-block:: yaml

        # config/packages/knp_menu.yaml
        knp_menu:
            # use "twig: false" to disable the Twig extension and the TwigRenderer
            twig:
                template: KnpMenuBundle::menu.html.twig
            # if true, enables the helper for PHP templates
            # support for templating is deprecated, it will be removed in next major version
            templating: false
            # the renderer to use, list is also available by default
            default_renderer: twig

    .. code-block:: xml

        <!-- config/packages/knp_menu.xml -->
        <?xml version="1.0" charset="UTF-8" ?>
        <container xmlns="http://symfony.com/schema/dic/services"
            xmlns:knp-menu="http://knplabs.com/schema/dic/menu">

            <!--
                templating:       if true, enable the helper for PHP templates (deprecated)
                default-renderer: the renderer to use, list is also available by default
            -->
            <knp-menu:config
                templating="false"
                default-renderer="twig"
            >
                <!-- add enabled="false" to disable the Twig extension and the TwigRenderer -->
                <knp-menu:twig template="KnpMenuBundle::menu.html.twig"/>
            </knp-menu:config>
        </container>

    .. code-block:: php

        // config/packages/knp_menu.php
        $container->loadFromExtension('knp_menu', [
            // use 'twig' => false to disable the Twig extension and the TwigRenderer
            'twig' => [
                'template' => 'KnpMenuBundle::menu.html.twig'
            ],
            // if true, enable the helper for PHP templates (deprecated)
            'templating' => false,
            // the renderer to use, list is also available by default
            'default_renderer' => 'twig',
        ]);

.. note::

    Take care to change the default renderer if you disable the Twig support.

Create your first menu!
-----------------------

There are two ways to create a menu: the "easy" way, and the more flexible
method of creating a menu as a service.

Method a) The Easy Way (yay)!
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

To create a menu, first create a new class in the ``Menu`` directory of one
of your bundles. This class - called ``Builder`` in our example - will have
one method for each menu that you need to build.

An example builder class would look like this:

.. code-block:: php

    // src/Menu/Builder.php
    namespace App\Menu;

    use App\Entity\Blog;
    use Knp\Menu\FactoryInterface;
    use Knp\Menu\ItemInterface;

    final class Builder
    {
        public function __construct(
            private EntityManagerInterface $em,
        ) {
        }

        public function mainMenu(FactoryInterface $factory, array $options): ItemInterface
        {
            $menu = $factory->createItem('root');

            $menu->addChild('Home', ['route' => 'homepage']);

            // findMostRecent and Blog are just imaginary examples
            $blog = $this->em->getRepository(Blog::class)->findMostRecent();

            $menu->addChild('Latest Blog Post', [
                'route' => 'blog_show',
                'routeParameters' => ['id' => $blog->getId()]
            ]);

            // create another menu item
            $menu->addChild('About Me', ['route' => 'about']);
            // you can also add sub levels to your menus as follows
            $menu['About Me']->addChild('Edit profile', ['route' => 'edit_profile']);

            // ... add more children

            return $menu;
        }
    }

With the standard ``knp_menu.html.twig`` template and your current page being
'Home', your menu would render with the following markup:

.. code-block:: html

    <ul>
        <li class="current first">
            <a href="#route_to/homepage">Home</a>
        </li>
        <li class="current_ancestor">
            <a href="#route_to/page_show/?id=42">About Me</a>
            <ul class="menu_level_1">
                <li class="current first last">
                    <a href="#route_to/edit_profile">Edit profile</a>
                </li>
            </ul>
        </li>
    </ul>

.. note::

    The menu builder can be overwritten using the bundle inheritance.

To actually render the menu, just do the following from anywhere in any template:

.. configuration-block::

    .. code-block:: html+jinja

        {{ knp_menu_render('App:Builder:mainMenu') }}

    .. code-block:: html+php

        <?php echo $view['knp_menu']->render('App:Builder:mainMenu') ?>

With this method, you refer to the menu using a three-part string:
**bundle**:**class**:**method**.

If you needed to create a second menu, you'd simply add another method to
the ``Builder`` class (e.g. ``sidebarMenu``), build and return the new menu,
then render it via ``App:Builder:sidebarMenu``.

That's it! The menu is *very* configurable. For more details, see the
`KnpMenu documentation`_.

Method b) A menu builder as a service
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

For information on how to register a menu builder as a service, read
:doc:`Creating Menu Builders as Services <menu_builder_service>`.


Method c) A menu as a service
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

For information on how to register a service and tag it as a menu, read
:doc:`Creating Menus as Services <menu_service>`.

.. note::

    To improve performances, you can :doc:`disable providers you don't need <disabling_providers>`.

Rendering Menus
---------------

Once you've set up your menu, rendering it is easy. If you've used the "easy"
way, then do the following:

.. configuration-block::

    .. code-block:: html+jinja

        {{ knp_menu_render('App:Builder:mainMenu') }}

    .. code-block:: html+php

        <?php echo $view['knp_menu']->render('App:Builder:mainMenu') ?>

Additionally, you can pass some options to the renderer:

.. configuration-block::

    .. code-block:: html+jinja

        {{ knp_menu_render('App:Builder:mainMenu', {'depth': 2, 'currentAsLink': false}) }}

    .. code-block:: html+php

        <?php echo $view['knp_menu']->render('App:Builder:mainMenu', [
            'depth'         => 2,
            'currentAsLink' => false,
        ]) ?>

For a full list of options, see the "Other rendering options" header on the
`KnpMenu documentation`_.

You can also "get" a menu, which you can use to render later:

.. configuration-block::

    .. code-block:: html+jinja

        {% set menuItem = knp_menu_get('App:Builder:mainMenu') %}
        {{ knp_menu_render(menuItem) }}

    .. code-block:: html+php

        <?php $menuItem = $view['knp_menu']->get('App:Builder:mainMenu') ?>
        <?php echo $view['knp_menu']->render($menuItem) ?>

If you want to only retrieve a certain branch of the menu, you can do the
following, where 'Contact' is one of the root menu items and has children
beneath it.

.. configuration-block::

    .. code-block:: html+jinja

        {% set menuItem = knp_menu_get('App:Builder:mainMenu', ['Contact']) %}
        {{ knp_menu_render(['App:Builder:mainMenu', 'Contact']) }}

    .. code-block:: html+php

        <?php $menuItem = $view['knp_menu']->get('App:Builder:mainMenu', ['Contact']) ?>
        <?php echo $view['knp_menu']->render(['App:Builder:mainMenu', 'Contact']) ?>

If you want to pass some options to the builder, you can use the third parameter
of the ``knp_menu_get`` function:

.. configuration-block::

    .. code-block:: html+jinja

        {% set menuItem = knp_menu_get('App:Builder:mainMenu', [], {'some_option': 'my_value'}) %}
        {{ knp_menu_render(menuItem) }}

    .. code-block:: html+php

        <?php $menuItem = $view['knp_menu']->get('App:Builder:mainMenu', [], [
            'some_option' => 'my_value'
        ]) ?>
        <?php echo $view['knp_menu']->render($menuItem) ?>

More Advanced Stuff
-------------------

.. toctree::
    :maxdepth: 1

    menu_service
    menu_builder_service
    i18n
    events
    custom_renderer
    custom_provider
    disabling_providers

.. _`installation chapter`: https://getcomposer.org/doc/00-intro.md
.. _`Flex`: https://symfony.com/doc/current/setup/flex.html
.. _`KnpMenu documentation`: https://github.com/KnpLabs/KnpMenu/blob/master/doc/01-Basic-Menus.md
