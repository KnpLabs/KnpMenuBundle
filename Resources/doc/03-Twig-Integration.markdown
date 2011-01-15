Twig Integration
================

Services tagged as `menu` will be added automatically to the Twig helper. You
can access those menus with the `{% menu %}` tag in your Twig templates,
followed by the name of your service. 

Here is a complete but simple example for a Menu named `main`, used as your
main navigation for the whole page (expecting you have a `MainBundle` where you
store all your menus).

Enable both Dependency Injection extensions in your `config.yml`:

    # app/config/config.yml
    main.menu: ~
    menu.twig: ~


Create a Dependency Injection `MainExtension` and a `menuLoad()` function:

    <?php // src/Application/MainBundle/DependencyInjection/MainExtension.php
    
    namespace Application\MainBundle\DependencyInjection;
    
    use Symfony\Component\DependencyInjection\Extension\Extension,
        Symfony\Component\DependencyInjection\Loader\XmlFileLoader,
        Symfony\Component\DependencyInjection\ContainerBuilder;
    
    class MainExtension extends Extension
    {
        public function menuLoad($config, ContainerBuilder $container)
        {
            $loader = new XmlFileLoader($container, __DIR__.'/../Resources/config');
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
            return 'main';
        }
    }


Create a `MainMenu` class for your `main` menu:

    <?php // src/Application/MainBundle/Menu/MainMenu.php
    
    namespace Application\MainBundle\Menu;
    
    use Bundle\MenuBundle\Menu;
    
    use Symfony\Component\HttpFoundation\Request,
        Symfony\Component\Routing\Router;
    
    class MainMenu extends Menu
    {
        public function __construct(Request $request, Router $router)
        {
            parent::__construct();
            
            $this->setCurrentUri($request->getRequestUri());
            
            $this->addChild('Home', $router->generate('homepage'));
            // ... add more childs
        }
    }


Describe your `main` menu as a Service:

    <!-- src/Application/MainBundle/Resources/config/menu.xml -->
    <?xml version="1.0" encoding="UTF-8"?>
    <container xmlns="http://www.symfony-project.org/schema/dic/services"
        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:schemaLocation="http://www.symfony-project.org/schema/dic/services http://www.symfony-project.org/schema/dic/services/services-1.0.xsd">
        
        <parameters>
            <parameter key="menu.main.class">Application\MainBundle\Menu\MainMenu</parameter>
        </parameters>
        
        <services>
            <service id="menu.main" class="%menu.main.class%" shared="true">
                <tag name="menu" alias="main" />
                <argument type="service" id="request" />
                <argument type="service" id="router" />
            </service>
        </services>
    </container>

> **NOTICE:** the `<tag>` attributes are imported. Tagging your menu with the
> name `menu` tells the Twig helper to load this menu, and the alias `main` is
> used in your template to tell the helper which one to render.


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

