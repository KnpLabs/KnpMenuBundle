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

    $ composer require knplabs/knp-menu-bundle

This command requires you to have Composer installed globally, as explained
in the ``installation chapter``_ of the Composer documentation.

Step 2: Enable the Bundle
~~~~~~~~~~~~~~~~~~~~~~~~~

Then, enable the bundle by adding the following line in the ````app/AppKernel.php````
file of your project:

.. code-block:: php

    <?php
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

.. code-block:: yaml

    # app/config/config.yml
    knp_menu:
        twig:  # use "twig: false" to disable the Twig extension and the TwigRenderer
            template: knp_menu.html.twig
        templating: false # if true, enables the helper for PHP templates
        default_renderer: twig # The renderer to use, list is also available by default

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

    <?php
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

            // you can also add sub level's to your menu's as follows
            $menu['About Me']->addChild('Edit profile', array('route' => 'edit_profile'));

            // ... add more children

            return $menu;
        }
    }

With the standard ``knp_menu.html.twig`` template and your current page being
'Home' you're menu would render with the ollowing markup:

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

To actually render the menu, just do the following from anywhere in any Twig
template:

.. code-block:: html+jinja

    {{ knp_menu_render('AppBundle:Builder:mainMenu') }}

With this method, you refer to the menu using a three-part string:
**bundle**:**class**:**method**.

If you needed to create a second menu, you'd simply add another method to
the ``Builder`` class (e.g. ``sidebarMenu``), build and return the new menu,
then render it via ``AppBundle:Builder:sidebarMenu``.

That's it! The menu is *very* configurable. For more details, see the
`KnpMenu documentation`_.

Method b) A menu as a service
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

For information on how to register a service and tag it as a menu, read
`Creating Menus as Services <menu_service>`_.

Rendering Menus
---------------

Once you've setup your menu, rendering it easy. If you've used the "easy"
way, then do the following:

.. code-block:: html+jinja

    {{ knp_menu_render('AppBundle:Builder:mainMenu') }}

Additionally, you can pass some options to the renderer:

.. code-block:: html+jinja

    {{ knp_menu_render('AppBundle:Builder:mainMenu', {'depth': 2, 'currentAsLink': false}) }}

For a full list of options, see the "Other rendering options" header on the
`KnpMenu documentation`_.

You can also "get" a menu, which you can use to render later:

.. code-block:: html+jinja

    {% set menuItem = knp_menu_get('AppBundle:Builder:mainMenu') %}
    {{ knp_menu_render(menuItem) }}

If you want to only retrieve a certain branch of the menu, you can do the
following, where 'Contact' is one of the root menu items and has children
beneath it.

.. code-block:: html+jinja

    {% set menuItem = knp_menu_get('AppBundle:Builder:mainMenu', ['Contact']) %}
    {{ knp_menu_render(['AppBundle:Builder:mainMenu', 'Contact']) }}

If you want to pass some options to the builder, you can use the third parameter
of the ``knp_menu_get`` function:

.. code-block:: html+jinja

    {% set menuItem = knp_menu_get('AppBundle:Builder:mainMenu', [], {'some_option': 'my_value'}) %}
    {{ knp_menu_render(menuItem) }}

Using PHP templates
-------------------

If you prefer using PHP templates, you can use the templating helper to render
and retrieve your menu from a template, just like available in Twig.

.. code-block:: php

    // Retrieves an item by its path in the main menu
    $item = $view['knp_menu']->get('AppBundle:Builder:main', array('child'));

    // Render an item
    echo $view['knp_menu']->render($item, array(), 'list');

More Advanced Stuff
-------------------

* :doc:`Menus as Services <menu_service>`_
* :doc:`Custom Menu Renderer <custom_renderer>`_
* :doc:`Custom Menu Provider <custom_provider>`_
* :doc:`I18n for your menu labels <i18n>`_
* :doc:`Using events to allow extending the menu <events>`_

.. _````installation chapter````: https://getcomposer.org/doc/00-intro.md
.. _`KnpMenu documentation`: https://github.com/KnpLabs/KnpMenu/blob/master/doc/01-Basic-Menus.markdown
