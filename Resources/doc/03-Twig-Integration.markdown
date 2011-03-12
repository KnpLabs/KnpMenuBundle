Twig Integration
================

Services tagged as `menu` will be added automatically to the Twig helper. You
can access those menus with the `{{ menu('menu_alias') }}` tag in your Twig templates.

Here is a complete but simple example for a Menu named `main`, used as your
main navigation for the whole page (expecting you have a `MyVendor\MyBundle` bundle
where you store your menu).

Create a Menu class
-------------------

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

Declare a Menu service
----------------------

First create a `menu.xml` to declare your `menu.main` service:

    # src/MyVendor/MyBundle/Resources/config/menu.xml
    <?xml version="1.0" ?>

    <container xmlns="http://symfony.com/schema/dic/services"
        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:schemaLocation="http://symfony.com/schema/dic/services http://symfony.com/schema/dic/services/services-1.0.xsd">

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


If you include the menu configuration in your bundle (as shown above), you'll
need to include it as a resource in your base configuration:

    # app/config/config.yml
    imports:
        - { resource: "@MyVendorMyBundle/Resources/config/menu.xml" }
    ...


Configure the bundle to use Twig
--------------------------------

Finally you should enable the Twig extension of the bundle:

    # app/config/config.yml
    knplabs_menu:
        twig: true

Render your menu with Twig
--------------------------

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

Render your menu using the Renderer of the MenuItem object
----------------------------------------------------------

You can also use the Renderer which allows you not to use the PHP templating
engine and to customize the rendering by changing the renderer of the menu.
Just get the menu object and call the ``render`` method:

    {# app/views/layout.twig #}
    <html>
        {# ... #}
        <body>
            {# ... #}
            <nav id="main">
                {{ menu_get('main').render|raw }}
            </nav>
            {# ... #}
        </body>
    </html>

> Using the ``raw`` filter is needed when the autoescaping is enabled as the
> ``render`` method returns HTML code.
