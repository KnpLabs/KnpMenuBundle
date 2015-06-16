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

    $ composer require knplabs/knp-menu-bundle "~2"

This command requires you to have Composer installed globally, as explained
in the `installation chapter`_ of the Composer documentation.

Step 2: Enable the Bundle
~~~~~~~~~~~~~~~~~~~~~~~~~

Then, enable the bundle by adding the following line in the ``app/AppKernel.php``
file of your project:

.. code-block:: php

    // app/AppKernel.php

    // ...
    class AppKernel extends Kernel
    {
        public function registerBundles()
        {
            $bundles = array(
                // ...

                new Knp\Bundle\MenuBundle\KnpMenuBundle(),
            );

            // ...
        }

        // ...
    }

Step 3: (optional) Configure the bundle
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

The bundle comes with a sensible default configuration, which is listed below.
If you skip this step, these defaults will be used.

.. configuration-block::

    .. code-block:: yaml

        # app/config/config.yml
        knp_menu:
            # use "twig: false" to disable the Twig extension and the TwigRenderer
            twig:
                template: knp_menu.html.twig
            #  if true, enables the helper for PHP templates
            templating: false
            # the renderer to use, list is also available by default
            default_renderer: twig

    .. code-bock:: xml

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
                <knp-menu:twig template="knp_menu.html.twig"/>
            </knp-menu:config>
        </container>

    .. code-block:: php

        // app/config/config.php
        $container->loadFromExtension('knp_menu', array(
            // use 'twig' => false to disable the Twig extension and the TwigRenderer
            'twig' => array(
                'template' => 'knp_menu.html.twig'
            ),
            // if true, enabled the helper for PHP templates
            'templating' => false,
            // the renderer to use, list is also available by default
            'default_renderer' => 'twig',
        ));

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

    // src/AppBundle/Menu/Builder.php
    namespace AppBundle\Menu;

    use Knp\Menu\FactoryInterface;
    use Symfony\Component\DependencyInjection\ContainerAware;

    class Builder extends ContainerAware
    {
        public function mainMenu(FactoryInterface $factory, array $options)
        {
            $menu = $factory->createItem('root');

            $menu->addChild('Home', array('route' => 'homepage'));

            // access services from the container!
            $em = $this->container->get('doctrine')->getManager();
            // findMostRecent and Blog are just imaginary examples
            $blog = $em->getRepository('AppBundle:Blog')->findMostRecent();

            $menu->addChild('Latest Blog Post', array(
                'route' => 'blog_show',
                'routeParameters' => array('id' => $blog->getId())
            ));

            // create another menu item
            $menu->addChild('About Me', array('route' => 'about'));
            // you can also add sub level's to your menu's as follows
            $menu['About Me']->addChild('Edit profile', array('route' => 'edit_profile'));

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

    You only need to extend ``ContainerAware`` if you need the service
    container to be available via ``$this->container``. You can also implement
    ``ContainerAwareInterface`` instead of extending this class.

.. note::

    The menu builder can be overwritten using the bundle inheritance.

To actually render the menu, just do the following from anywhere in any template:

.. configuration-block::

    .. code-block:: html+jinja

        {{ knp_menu_render('AppBundle:Builder:mainMenu') }}

    .. code-block:: html+php

        <?php $view['knp_menu']->render('AppBundle:Builder:mainMenu') ?>

With this method, you refer to the menu using a three-part string:
**bundle**:**class**:**method**.

If you needed to create a second menu, you'd simply add another method to
the ``Builder`` class (e.g. ``sidebarMenu``), build and return the new menu,
then render it via ``AppBundle:Builder:sidebarMenu``.

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

Once you've setup your menu, rendering it easy. If you've used the "easy"
way, then do the following:

.. configuration-block::

    .. code-block:: html+jinja

        {{ knp_menu_render('AppBundle:Builder:mainMenu') }}

    .. code-block:: html+php

        <?php $view['knp_menu']->render('AppBundle:Builder:mainMenu') ?>

Additionally, you can pass some options to the renderer:

.. configuration-block::

    .. code-block:: html+jinja

        {{ knp_menu_render('AppBundle:Builder:mainMenu', {'depth': 2, 'currentAsLink': false}) }}

    .. code-block:: html+php

        <?php $view['knp_menu']->render('AppBundle:Builder:mainMenu', array(
            'depth'         => 2,
            'currentAsLink' => false,
        )) ?>

For a full list of options, see the "Other rendering options" header on the
`KnpMenu documentation`_.

You can also "get" a menu, which you can use to render later:

.. configuration-block::

    .. code-block:: html+jinja

        {% set menuItem = knp_menu_get('AppBundle:Builder:mainMenu') %}
        {{ knp_menu_render(menuItem) }}

    .. code-block:: html+php

        <?php $menuItem = $view['knp_menu']->get('AppBundle:Builder:mainMenu') ?>
        <?php $view['knp_menu']->render($menuItem) ?>

If you want to only retrieve a certain branch of the menu, you can do the
following, where 'Contact' is one of the root menu items and has children
beneath it.

.. configuration-block::

    .. code-block:: html+jinja

        {% set menuItem = knp_menu_get('AppBundle:Builder:mainMenu', ['Contact']) %}
        {{ knp_menu_render(['AppBundle:Builder:mainMenu', 'Contact']) }}

    .. code-block:: html+php

        <?php $menuItem = $view['knp_menu']->get('AppBundle:Builder:mainMenu', array('Contact')) ?>
        <?php $view['knp_menu']->render(array('AppBundle:Builder:mainMenu', 'Contact')) ?>

If you want to pass some options to the builder, you can use the third parameter
of the ``knp_menu_get`` function:

.. configuration-block::

    .. code-block:: html+jinja

        {% set menuItem = knp_menu_get('AppBundle:Builder:mainMenu', [], {'some_option': 'my_value'}) %}
        {{ knp_menu_render(menuItem) }}

    .. code-block:: html+php

        <?php $menuItem = $view['knp_menu']->get('AppBundle:Builder:mainMenu', array(), array(
            'some_option' => 'my_value'
        )) ?>
        <?php $view['knp_menu']->render($menuItem) ?>

More Advanced Stuff
-------------------

* :doc:`Menus as Services <menu_service>`
* :doc:`Custom Menu Renderer <custom_renderer>`
* :doc:`Custom Menu Provider <custom_provider>`
* :doc:`I18n for your menu labels <i18n>`
* :doc:`Using events to allow extending the menu <events>`

.. _`installation chapter`: https://getcomposer.org/doc/00-intro.md
.. _`KnpMenu documentation`: https://github.com/KnpLabs/KnpMenu/blob/master/doc/01-Basic-Menus.markdown
