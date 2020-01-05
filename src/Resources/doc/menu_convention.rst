Convention-based menus
======================

For apps using bundles, menus can be defined using a special convention
instead of registering them as services.

.. note::

    For projects not using a bundle (Flex projects for instance), this
    way of defining menus does not work. Register your builders as services
    instead.

To create a menu, first create a new class in the ``Menu`` directory of one
of your bundles. This class - called ``Builder`` in our example - will have
one method for each menu that you need to build.

An example builder class would look like this:

.. code-block:: php

    // src/AppBundle/Menu/Builder.php
    namespace AppBundle\Menu;

    use Knp\Menu\FactoryInterface;
    use Symfony\Component\DependencyInjection\ContainerAwareInterface;
    use Symfony\Component\DependencyInjection\ContainerAwareTrait;

    class Builder implements ContainerAwareInterface
    {
        use ContainerAwareTrait;

        public function mainMenu(FactoryInterface $factory, array $options)
        {
            $menu = $factory->createItem('root');

            $menu->addChild('Home', ['route' => 'homepage']);

            // access services from the container!
            $em = $this->container->get('doctrine')->getManager();
            // findMostRecent and Blog are just imaginary examples
            $blog = $em->getRepository('AppBundle:Blog')->findMostRecent();

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

    You only need to implement ``ContainerAwareInterface`` if you need the
    service container. The more elegant way to handle your dependencies is to
    inject them in the constructor. If you want to do that, see method below.

.. note::

    The menu builder can be overwritten using the bundle inheritance.

To actually render the menu, just do the following from anywhere in any template:

.. configuration-block::

    .. code-block:: html+jinja

        {{ knp_menu_render('AppBundle:Builder:mainMenu') }}

    .. code-block:: html+php

        <?php echo $view['knp_menu']->render('AppBundle:Builder:mainMenu') ?>

With this method, you refer to the menu using a three-part string:
**bundle**:**class**:**method**.

If you needed to create a second menu, you'd simply add another method to
the ``Builder`` class (e.g. ``sidebarMenu``), build and return the new menu,
then render it via ``AppBundle:Builder:sidebarMenu``.

That's it! The menu is *very* configurable. For more details, see the
`KnpMenu documentation`_.

.. _`KnpMenu documentation`: https://github.com/KnpLabs/KnpMenu/blob/master/doc/01-Basic-Menus.markdown
