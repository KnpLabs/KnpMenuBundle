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

    $ composer require knplabs/knp-menu-bundle "^2.0"

This command requires you to have Composer installed globally, as explained
in the `installation chapter`_ of the Composer documentation.

Step 2: Enable the Bundle
~~~~~~~~~~~~~~~~~~~~~~~~~

.. note::

    This step is performed for you automatically when using Flex.

Then, enable the bundle by adding the following line in the ``app/AppKernel.php``
file of your project:

.. code-block:: php

    // app/AppKernel.php

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

        # app/config/config.yml
        knp_menu:
            # use "twig: false" to disable the Twig extension and the TwigRenderer
            twig:
                template: KnpMenuBundle::menu.html.twig
            #  if true, enables the helper for PHP templates
            templating: false
            # the renderer to use, list is also available by default
            default_renderer: twig

    .. code-block:: xml

        <!-- app/config/config.xml -->
        <?xml version="1.0" charset="UTF-8" ?>
        <container xmlns="http://symfony.com/schema/dic/services"
            xmlns:knp-menu="http://knplabs.com/schema/dic/menu">

            <!--
                templating:       if true, enabled the helper for PHP templates
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

        // app/config/config.php
        $container->loadFromExtension('knp_menu', [
            // use 'twig' => false to disable the Twig extension and the TwigRenderer
            'twig' => [
                'template' => 'KnpMenuBundle::menu.html.twig'
            ],
            // if true, enabled the helper for PHP templates
            'templating' => false,
            // the renderer to use, list is also available by default
            'default_renderer' => 'twig',
        ]);

.. versionadded::2.1.2

    The template used to be ``knp_menu.html.twig`` which did not translate menu entries.
    Version 2.1.2 adds the template that translates menu entries.

.. note::

    Take care to change the default renderer if you disable the Twig support.

Create your first menu!
-----------------------

There are two ways to create a menu: the "easy" way, and the more flexible
method of creating a menu as a service.

Method a) The Easy Way (yay)!
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

To create a menu, first create a class implementing
``Knp\Bundle\MenuBundle\MenuBuilderProviderInterface``. This class - called
``Builder`` in our example - will have one method for each menu that you
need to build.

An example builder class would look like this:

.. code-block:: php

    // src/App/Menu/Builder.php
    namespace App\Menu;

    use Knp\Bundle\MenuBundle\MenuBuilderProviderInterface;
    use Knp\Menu\FactoryInterface;

    class Builder implements MenuBuilderProviderInterface
    {
        private $factory;

        public function __construct(FactoryInterface $factory)
        {
            $this->factory = $factory;
        }

        public static function getMenuBuilders()
        {
            return [
                'main' => 'buildMainMenu',
            ];
        }

        public function buildMainMenu(array $options)
        {
            $menu = $this->factory->createItem('root');

            $menu->addChild('Home', ['route' => 'homepage']);

            // create another menu item
            $menu->addChild('About Me', ['route' => 'about']);
            // you can also add sub levels to your menus as follows
            $menu['About Me']->addChild('Edit profile', ['route' => 'edit_profile']);

            // ... add more children

            return $menu;
        }
    }

To register this menu builder, it needs to be defined as a service tagged
with ``knp_menu.menu_builder_provider``. If you use Symfony 3.3+, the tag
will be added automatically when using auto-configuration thanks to detecting
the interface.

.. note::

    In a Flex project, you won't need to do anything to register the builder,
    as all your classes are already registered as services by default.

With the standard ``knp_menu.html.twig`` template and your current page being
'Home', your menu would render with the following markup:

.. code-block:: html

    <ul>
        <li class="first">
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

To actually render the menu, just do the following from anywhere in any template:

.. configuration-block::

    .. code-block:: html+jinja

        {{ knp_menu_render('main') }}

    .. code-block:: html+php

        <?php echo $view['knp_menu']->render('main') ?>

If you needed to create a second menu, you'd simply add another method to
the ``Builder`` class (e.g. ``sidebarMenu``) and register it in ``getMenuBuilders``.

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

Method d) A menu based on a convention for builders
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

For projects using a bundle, menu builders can also be accessed based on
a naming convention. For more information, read :doc:`Convention-based menus <menu_convention>`.

.. note::

    To improve performances, you can :doc:`disable providers you don't need <disabling_providers>`.

Rendering Menus
---------------

Once you've setup your menu, rendering it easy. If you've used the "easy"
way, then do the following:

.. configuration-block::

    .. code-block:: html+jinja

        {{ knp_menu_render('AppBundle:Builder:mainMenu') }}

    .. code-block:: html+php

        <?php echo $view['knp_menu']->render('AppBundle:Builder:mainMenu') ?>

Additionally, you can pass some options to the renderer:

.. configuration-block::

    .. code-block:: html+jinja

        {{ knp_menu_render('AppBundle:Builder:mainMenu', {'depth': 2, 'currentAsLink': false}) }}

    .. code-block:: html+php

        <?php echo $view['knp_menu']->render('AppBundle:Builder:mainMenu', [
            'depth'         => 2,
            'currentAsLink' => false,
        ]) ?>

For a full list of options, see the "Other rendering options" header on the
`KnpMenu documentation`_.

You can also "get" a menu, which you can use to render later:

.. configuration-block::

    .. code-block:: html+jinja

        {% set menuItem = knp_menu_get('AppBundle:Builder:mainMenu') %}
        {{ knp_menu_render(menuItem) }}

    .. code-block:: html+php

        <?php $menuItem = $view['knp_menu']->get('AppBundle:Builder:mainMenu') ?>
        <?php echo $view['knp_menu']->render($menuItem) ?>

If you want to only retrieve a certain branch of the menu, you can do the
following, where 'Contact' is one of the root menu items and has children
beneath it.

.. configuration-block::

    .. code-block:: html+jinja

        {% set menuItem = knp_menu_get('AppBundle:Builder:mainMenu', ['Contact']) %}
        {{ knp_menu_render(['AppBundle:Builder:mainMenu', 'Contact']) }}

    .. code-block:: html+php

        <?php $menuItem = $view['knp_menu']->get('AppBundle:Builder:mainMenu', ['Contact']) ?>
        <?php echo $view['knp_menu']->render(['AppBundle:Builder:mainMenu', 'Contact']) ?>

If you want to pass some options to the builder, you can use the third parameter
of the ``knp_menu_get`` function:

.. configuration-block::

    .. code-block:: html+jinja

        {% set menuItem = knp_menu_get('AppBundle:Builder:mainMenu', [], {'some_option': 'my_value'}) %}
        {{ knp_menu_render(menuItem) }}

    .. code-block:: html+php

        <?php $menuItem = $view['knp_menu']->get('AppBundle:Builder:mainMenu', [], [
            'some_option' => 'my_value'
        ]) ?>
        <?php echo $view['knp_menu']->render($menuItem) ?>

More Advanced Stuff
-------------------

.. toctree::
    :maxdepth: 1

    menu_service
    menu_builder_service
    menu_convention
    i18n
    events
    custom_renderer
    custom_provider
    disabling_providers

.. _`installation chapter`: https://getcomposer.org/doc/00-intro.md
.. _`KnpMenu documentation`: https://github.com/KnpLabs/KnpMenu/blob/master/doc/01-Basic-Menus.markdown
