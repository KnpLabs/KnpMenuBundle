Using menus with Symfony2
=========================

The core menu classes of the MenuBundle, `Bundle\MenuBundle\Menu` and
`Bundle\MenuBundle\MenuItem`, are perfectly decoupled from Symfony2 and
can be used in any PHP 5.3 project.

This bundle also provides several classes that ease the integration of
menus within a Symfony2 project.

## Make your menu a service

There a lot of benefits to making a menu a service. Its logic is then
self-contained,and it can be accessed from anywhere in the project.

### Create your menu class

    // src/Application/MyBundle/Menu/MainMenu.php
    <?php
    namespace Application\MyBundle\Menu;
    use Bundle\MenuBundle\Menu;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\Routing\Router;

    class MainMenu extends Menu
    {
        public function __construct(Request $request, Router $router)
        {
            parent::__construct();
            
            $this->setCurrentUri($request->getRequestUri());
            
            $this->addChild('Home', $router->generate('homepage'));
            $this->addChild('Comments', $router->generate('comments'));
        }
    }

The construction of the menu items is now contained inside the menu.
It requires a Symfony Router in order to generate uris.

### Declare and configure your menu service

Next, declare you menu service class via configuration. An example in XML
is shown below:

    # src/Application/MyBundle/Resources/config/menu.xml
    ...

    <parameters>
        <parameter key="menu.main.class">Application\MyBundle\Menu\MainMenu</parameter>
    </parameters>

    <services>
        <service id="menu.main" class="%menu.main.class%" shared="true">
            <tag name="menu" alias="main" />
            <argument type="service" id="request" />
            <argument type="service" id="router" />
        </service>
    </services>

    ...

If you include the menu configuration in your bundle (as shown above), you'll
need to include it as a resource in your base configuration:

    # app/config/config.xml
    ...
    
    <import resource="MyBundle/Resources/config/menu.xml" />

### Access the menu service

You can now access your menu like any Symfony service:

    $menu = $container->get('menu.main');

From a controller, it's even easier:

    $menu = $this['menu.main']

The menu is lazy loaded, and will construct its children the first time
you access it.

## Create a template helper for your menu

You will probably need to access the menu from a template.
You _could_ render an entire action to get the menu from a controller class,
pass it to a template and the render it. But it would be a bit overkill.
You can easily enable a Symfony template helper instead. This bundle
provides a generic menu template helper, all you need to do is enable the helper.

### Enable the menu template helper

    # app/config/config.yml
    menu.templating: ~

### Access the menu from a template

You now can render the menu in a template:

    echo $view['menu']->get('main')->render()

Or manipulate it:

    $view['menu']['main']['Home']->setLabel('<span>Home</span>');
    $view['menu']['main']['Home']->setIsCurrent(true);

## Customize your Menu

If you want to customize the way your menu are rendered, just create a
custom `MenuItem` class

    # src/Application/MyBundle/Menu/MyCustomMenuItem.php
    <?php
    namespace Application\MyBundle\Menu;
    use Bundle\MenuBundle\MenuItem;

    class MyCustomMenuItem extends MenuItem
    {
      /**
       * Renders the anchor tag for this menu item.
       *
       * If no uri is specified, or if the uri fails to generate, the
       * label will be output.
       *
       * @return string
       */
      public function renderLink()
      {
        $label = $this->renderLabel();
        $uri = $this->getUri();
        if (!$uri) {
          return $label;
        }

        return sprintf('<a href="%s"><span></span>%s</a>', $uri, $label);
      }
    }

This example overrides the `renderLink()` method. You can then use the new
`CustomMenuItem` class as the default item class in your `MainMenu`:

    // src/Application/MyBundle/Menu/MainMenu.php
    <?php
    namespace Application\MyBundle\Menu;
    use Bundle\MenuBundle\Menu;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\Routing\Router;
    
    class MainMenu extends Menu
    {
        public function __construct(Request $request, Router $router)
        {
            parent::__construct(array(), 'Application\MyBundle\Menu\MyCustomMenuItem');
            
            $this->setCurrentUri($request->getRequestUri());
            
            $this->addChild('Home', $router->generate('homepage'));
            $this->addChild('Comments', $router->generate('comments'));
      }
    }

Or, if you want to customize each child item, pass them as an argument of
the `addChild()` method:

    // src/Application/MyBundle/Menu/MainMenu.php
    <?php
    namespace Application\MyBundle\Menu;
    use Bundle\MenuBundle\Menu;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\Routing\Router;

    class MainMenu extends Menu
    {
        public function __construct(Request $request, Router $router)
        {
            parent::__construct();
            
            $this->setCurrentUri($request->getRequestUri());
            
            $this->addChild(new MyCustomMenuItem('Home', $router->generate('homepage')));
            $this->addChild(new MyCustomMenuItem('Comments', $router->generate('comments')));
        }
    }

