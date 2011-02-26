Twig Integration
================

Services tagged as `menu` will be added automatically to the Twig helper. You
can access those menus with the `{{ menu('menu_alias') }}` tag in your Twig templates.

Here is a complete but simple example for a Menu named `main`, used as your
main navigation for the whole page (expecting you have a `MyVendor\MyBundle` bundle
where you store your menu).

The Menu class
--------------

Create a `MainMenu` class for your `main` menu:

    <?php // src/MyVendor/MyBundle/Menu/MainMenu.php
    
    namespace MyVendor\MyBundle\Menu;
    
    use Knplabs\MenuBundle\Menu;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\Routing\Router;
    
    class MainMenu extends Menu
    {
        /**
         * @param Request $request
         * @param Router $router
         */
        public function __construct(Request $request, Router $router)
        {
            parent::__construct();
            
            $this->setCurrentUri($request->getRequestUri());
            
            $this->addChild('Home', $router->generate('homepage'));
            // ... add more children
        }
    }

The Menu service
----------------

First create a `menu.xml` to declare your `menu.main` service:

    # src/MyVendor/MyBundle/Resources/config/menu.xml
    <?xml version="1.0" ?>

    <container xmlns="http://www.symfony-project.org/schema/dic/services"
        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:schemaLocation="http://www.symfony-project.org/schema/dic/services http://www.symfony-project.org/schema/dic/services/services-1.0.xsd">

        <parameters>
            <parameter key="menu.main.class">MyVendor\MyBundle\Menu\MainMenu</parameter>
        </parameters>

        <services>
            <service id="menu.main" class="%menu.main.class%" scope="request">
                <tag name="menu" alias="main" />
                <argument type="service" id="request" />
                <argument type="service" id="router" />
            </service>
        </services>

    </container>

> **About `<tag>`:** Tagging your menu with the name `menu` tells
> the Twig helper to load this menu and give it the alias `main`.
> This way you can use a simple alias in your template to tell the twig helper
> to render THIS menu.

The Dependency Injection
------------------------

Then you should create a Dependency Injection Extension to load your `menu.xml`
file when the bundle extensions are enabled.

    <?php // src/MyVendor/MyBundle/DependencyInjection/MainExtension.php

    namespace MyVendor\MyBundle\DependencyInjection;

    use Symfony\Component\DependencyInjection\Extension\Extension;
    use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
    use Symfony\Component\DependencyInjection\ContainerBuilder;
    use Symfony\Component\Config\FileLocator;

    class MyVendorMyBundleExtension extends Extension
    {
        public function load(array $config, ContainerBuilder $configuration)
        {
            $loader = new XmlFileLoader($configuration, new FileLocator(__DIR__.'/../Resources/config'));
            $loader->load('menu.xml');
        }

        public function getXsdValidationBasePath()
        {
            return null;
        }

        public function getNamespace()
        {
            return 'http://www.symfony-project.org/schema/dic/symfony';
        }

        public function getAlias()
        {
            return 'myvendor_mybundle';
        }
    }

Enabling the Dependency Injection
---------------------------------

Finally you should enable two extensions in your Dependency Injection config file:

* The `KnplabsMenuExtension` provided in `MenuBundle` to load twig helpers (aliased to `knplabs_menu`)
* The `MyVendorMyBundleExtension` we just wrote (aliased to `myvendor_mybundle`)

    # app/config/config.yml
    knplabs_menu:
        twig: true

    myvendor_mybundle: ~

Rendering
---------

Now its time to render the menu in your main `layout.twig`:

    {# app/views/layout.twig #}
    <html>
        {# ... #}
        <body>
            {# ... #}
            <nav id="main">
                {{ menu('main') }}
            </nav>
            {# ... #}
        </body>
    </html>


You can optionally provide a `depth` parameter to control how much of your menu
you want to render:

    {{ menu('main', 3) }}

