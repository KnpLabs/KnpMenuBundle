Registering your own provider
=============================

Registering your own menu provider allows you to feed your menu with your own
data, accessed by your code. It can for example go through a PHPCR repository
and create the corresponding menu elements.

Create first your Provider class, in the Provider directory of your bundle:

.. code-block:: php

    namespace AppBundle\Provider;

    use Knp\Menu\FactoryInterface;
    use Knp\Menu\Provider\MenuProviderInterface;

    class CustomMenuProvider implements MenuProviderInterface
    {
        /**
         * @var FactoryInterface
         */
        protected $factory = null;


        /**
         * @param FactoryInterface $factory the menu factory used to create the menu item
         */
        public function __construct(FactoryInterface $factory)
        {
            $this->factory = $factory;
        }

        /**
         * Retrieves a menu by its name
         *
         * @param string $name
         * @param array $options
         * @return \Knp\Menu\ItemInterface
         * @throws \InvalidArgumentException if the menu does not exists
         */
        public function get($name, array $options = array())
        {
            if ('demo' == $name) { //several menu could call this provider

                $menu = /* construct / get a \Knp\Menu\NodeInterface */;

                if ($menu === null) {
                    throw new \InvalidArgumentException(sprintf('The menu "%s" is not defined.', $name));
                }

                /*
                 * Populate your menu here
                 */

                $menuItem = $this->factory->createFromNode($menu);

                return $menuItem;
            }
        }

        /**
         * Checks whether a menu exists in this provider
         *
         * @param string $name
         * @param array $options
         * @return bool
         */
        public function has($name, array $options = array())
        {
            $menu = /* find the menu called $name */;

            return $menu !== null;
        }
    }

Then, configure the services linked to this new provider.

.. code-block:: yaml

    # app/config/services.yml
    services:
      app.menu_provider:
            class: AppBundle\Provider\CustomMenuProvider
            arguments:
              - @knp_menu.factory
            tags:
              - { name: knp_menu.provider }

    # ...

Finally, to generate the menu, for example inside a twig template type:

.. code-block:: html+jinja

    {{ knp_menu_render('demo') }}

The `Symfony CMF MenuBundle`_ provides a complete working example.

.. _`Symfony CMF MenuBundle`: https://github.com/symfony-cmf/MenuBundle
