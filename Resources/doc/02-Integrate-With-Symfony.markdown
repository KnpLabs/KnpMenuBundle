Integrate menus with Symfony2
==========================

Bundle\MenuBundle\Menu and Bundle\MenuBundle\MenuItem are perfectly decoupled from Symfony2.
They can be used in any PHP 5.3 project.

This bundle provides other classes that ease the integration of menus with a Symfony2 project.

## Make your menu a service

There a lot of benefits making a menu a service. Its logic is then self-contained, and it can be accessed from any place of the project.

### Create your menu class

    // src/Bundle/MyBundle/Menu/MainMenu.php
    <?php
    namespace Bundle\MyBundle\Menu;
    use Bundle\MenuBundle\Menu;
    use Symfony\Component\Routing\Router;

    class MainMenu extends Menu
    {
        public function __construct(Router $router)
        {
            parent::__construct();

            $this->addChild('Home', $router->generate('homepage'));
            $this->addChild('Comments', $router->generate('comments'));
        }
    }

The construction of the menu items is now contained inside the menu.
It requires a Symfony Router in order to generate uris.

### Declare and configure your menu service

    # src/Bundle/MyBundle/Resources/config/menu.xml
    ...

    <parameters>
        <parameter key="menu.main.class">Bundle\MyBundle\Menu\MainMenu</parameter>
    </parameters>

    <services>
        <service id="menu.main" class="%menu.main.class%" shared="true">
            <argument type="service" id="router" />
        </service>
    </services>

    ...

### Access the menu service

    You can now access your menu like any Symfony service:

        $menu = $container->get('menu.main');

    From a controller it's even easier:

        $menu = $this['menu.main']

    The menu is loazy loaded, and will construct its children the first time you access it.

## Create a template helper for your menu

You will probably need to access the menu from a template.
You may render a whole action to get the menu from a controller class,
pass it to a template and the render it.
But it would be a bit overkill. You can easily create a Symfony template helper instead.
This bundle provides a generic class for menu template helpers, all you need is
to declare the helper.

### Declare your menu template helper

    # src/Bundle/MyBundle/Resources/config/menu.xml
    <service id="templating.helper.main_menu" class="%templating.helper.menu.class%">
        <tag name="templating.helper" alias="main_menu" />
        <argument type="service" id="menu.main" />
        <argument>main_menu</argument>
    </service>

    # app/config/config.yml
    menu.templating: ~

### Access the menu from a template

You now can render the menu in a template:

    echo $view['main_menu']->render()

Or manipulate it:

    $view['main_menu']['Home']->setLabel('<span>Home</span>');
    $view['main_menu']['Home']->setIsCurrent(true);


### Customize your Menu

If you want to customize the way your menu are rendred, just create a custom MenuItem class

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

This example override the renderLink method.
Then use your new CustomMenuItem in your MainMenu class

    // src/Bundle/MyBundle/Menu/MainMenu.php
    <?php
    namespace Bundle\MyBundle\Menu;
    use Bundle\MenuBundle\Menu;
    use Symfony\Component\Routing\Router;

    class MainMenu extends Menu
    {
        public function __construct(Router $router)
        {
            parent::__construct();

            $this->addChild(new MyCustomMenuItem('Home', $router->generate('homepage')));
            $this->addChild(new MyCustomMenuItem('Comments', $router->generate('comments')));
        }
    }

