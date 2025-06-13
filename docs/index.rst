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

Method a) A menu builder as a service
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

For information on how to register a menu builder as a service, read
:doc:`Creating Menu Builders as Services <menu_builder_service>`.

Method b) A menu as a service
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

For information on how to register a service and tag it as a menu, read
:doc:`Creating Menus as Services <menu_service>`.

Method c) A menu discovered by convention
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

For information on how to use menu based on the bundle alias convention,
read :doc:`Creating Menu via Naming Convention <menu_convention>`.

.. note::

    To improve performances, you can :doc:`disable providers you don't need <disabling_providers>`.

Rendering Menus
---------------

Once you've set up your menu, rendering it is easy.

.. configuration-block::

    .. code-block:: html+jinja

        {{ knp_menu_render('my_main_menu') }}

    .. code-block:: html+php

        <?php echo $view['knp_menu']->render('my_main_menu') ?>

Additionally, you can pass some options to the renderer:

.. configuration-block::

    .. code-block:: html+jinja

        {{ knp_menu_render('my_main_menu', {'depth': 2, 'currentAsLink': false}) }}

    .. code-block:: html+php

        <?php echo $view['knp_menu']->render('my_main_menu', [
            'depth'         => 2,
            'currentAsLink' => false,
        ]) ?>

For a full list of options, see the "Other rendering options" header on the
`KnpMenu documentation`_.

You can also "get" a menu, which you can use to render later:

.. configuration-block::

    .. code-block:: html+jinja

        {% set menuItem = knp_menu_get('my_main_menu') %}
        {{ knp_menu_render(menuItem) }}

    .. code-block:: html+php

        <?php $menuItem = $view['knp_menu']->get('my_main_menu') ?>
        <?php echo $view['knp_menu']->render($menuItem) ?>

If you want to only retrieve a certain branch of the menu, you can do the
following, where 'Contact' is one of the root menu items and has children
beneath it.

.. configuration-block::

    .. code-block:: html+jinja

        {% set menuItem = knp_menu_get('my_main_menu', ['Contact']) %}
        {{ knp_menu_render(['my_main_menu', 'Contact']) }}

    .. code-block:: html+php

        <?php $menuItem = $view['knp_menu']->get('my_main_menu', ['Contact']) ?>
        <?php echo $view['knp_menu']->render(['my_main_menu', 'Contact']) ?>

If you want to pass some options to the builder, you can use the third parameter
of the ``knp_menu_get`` function:

.. configuration-block::

    .. code-block:: html+jinja

        {% set menuItem = knp_menu_get('my_main_menu', [], {'some_option': 'my_value'}) %}
        {{ knp_menu_render(menuItem) }}

    .. code-block:: html+php

        <?php $menuItem = $view['knp_menu']->get('my_main_menu', [], [
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
.. _`Flex`: https://symfony.com/doc/current/setup/flex.html
.. _`KnpMenu documentation`: https://github.com/KnpLabs/KnpMenu/blob/master/doc/01-Basic-Menus.md
