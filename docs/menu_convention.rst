Creating Menu via Naming Convention
===================================

KnpMenuBundle supports building menus using a naming convention to find
the class building the menu.

.. warning::

    The naming convention is relying on bundles. Project keeping their
    code outside a bundle (like in the Flex skeleton) cannot use this
    way of building menus.

To create a menu, first create a new class in the ``Menu`` directory of one
of your bundles. This class - called ``Builder`` in our example - will have
one method for each menu that you need to build.
The builder methods will receive the ``Knp\Menu\FactoryInterface`` as first
argument and an array of options as second argument.

An example builder class would look like this:

.. code-block:: php

    // src/AppBundle/Menu/Builder.php
    namespace AppBundle\Menu;

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

With this method, you refer to the menu using a three-part string:
**bundle**:**class**:**method**.

If you needed to create a second menu, you'd simply add another method to
the ``Builder`` class (e.g. ``sidebarMenu``), build and return the new menu,
then render it via ``App:Builder:sidebarMenu``.

You can now render the menu directly in a template via the its name:

.. code-block:: html+jinja

    {{ knp_menu_render('App:Builder:mainMenu') }}
