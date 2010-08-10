*Preface*

Like the whole codebase and unit tests, this documentation was ported from [ioMenuPlugin](http://github.com/weaverryan/ioMenuPlugin).

Creating Menus: The Basics
==========================

Let's face it, creating menus sucks. Menus - are common aspect of any
site - can range from simple, mundane tasks to giant monsters of headache
and code.

This bundle solves the issue by giving you a small, yet powerful and flexible
framework for handling your menus. While most of the examples shown here
are simple, the menus can grow arbitrarily large and deep.

Creating a menu
---------------

The menu framework centers around one main class: `Bundle\MenuBundle\MenuItem`. It's best
to think of each `MenuItem` object as an `<li>` tag that can hold children
objects (`<li>` tags that are wrapped in a `<ul>` tag). For example:

    use Bundle\MenuBundle\MenuItem;

    $menu = new MenuItem('My menu');
    $menu->addChild('overview', $router->generate('homepage'));
    $menu->addChild('comments', $router->generate('comments'));
    echo $menu->render();

The above would render the following html code:

    <ul class="menu">
      <li class="first">
        <a href="/">overview</a>
      </li>
      <li class="current last">
        <a href="/comments">comments</a>
      </li>
    </ul>

>**NOTE**
>The menu framework automatically adds `first` and `last` classes to each
>`<li>` tag at each level for easy styling. Notice also that a `current`
>class is added to the "current" menu item by url. The above example assumes
>the menu is being rendered on the `/comments` page, making the comments
>menu item "current".

>**NOTE**
>When the menu is rendered, it's actually spaced correctly so that it appears
>as shown in the source html. This is to allow for easier debugging and can
>be turned off by calling `ioMenuItem::setRenderCompressed(true)`.

Working with your menu tree
---------------------------

Your menu tree works and acts like a multi-dimensional array. Specifically,
it implements array access, countable and iterator: 

    $menu = new ioMenuItem('My menu');
    $menu->addChild('overview', $router->generate('homepage'));
    $menu->addChild('comments');
    
    $menu['comments']->setRoute($router->generate('comments'));
    $menu['comments']->addChild('My comments',$router->generate('my_comments'));
    
    echo count($menu); // returns 2

    foreach ($menu as $child) {
      echo $menu->getLabel();
    }

As you can see, the name you give your menu item (e.g. overview, comments)
when creating it is the name you'll use when accessing it. By default,
the name is also used when displaying the menu, but that can be overridden
by setting the menu item's label (see below).

Customizing each menu item
--------------------------

There are many ways to customize the output of each menu item.

### The label

By default, a menu item uses its name when rendering. You can easily
change this without changing the name of your menu item by setting its label:

    $menu->addChild('overview', $router->generate('homepage'));
    $menu['overview']->setLabel('Home');

### The route

When creating a new menu item (via the constructor or via `addChild()`),
the second argument is the url to your menu item. If a menu
isn't given a route, then text will be output instead of a link:

    $menu->addChild('overview');
    $menu->addChild('overview', $router->generate('homepage'));
    $menu->addChild('sympal', 'http://www.sympalphp.org');

You can also specify the route after creation via the `setRoute()` method:

    $menu['overview']->setRoute($router->generate('homepage'));

### Menu attributes

In fact, you can add any attribute to the `<li>` tag of a menu item. This
can be done as the optional 3rd argument when creating a menu item or
via the `setAttribute()` method:

    $menu->addChild('overview', null, array('id' => 'back_to_homepage'));
    $menu['overview']->setAttribute('id', 'back_to_homepage');

Rendering only part of a menu
-----------------------------

If you need to render only part of your menu, the menu framework gives
you unlimited control to do so:

    // render only 2 levels deep (root, parents, children)
    $menu->render(2);

    // rendering everything except for the children of the overview branch
    $menu['overview']->setShowChildren(false);
    $menu->render();

    // render everything except for overview AND its children
    $menu['overview']->setShow(false);
    $menu->render();

Using the above controls, you can specify exactly which part of you menu
you need to render at any given time.

The "root" is special
---------------------

Each menu is a tree containing exactly one root menu item. Let's revisit
the previous example:

    $menu = new MenuItem('My menu');
    $menu->addChild('overview', $router->generate('homepage'));
    $menu->addChild('comments', $router->generate('comments'));

In the above example, the `$menu` variable, corresponding to a menu item
named "My menu" is the root. The root node is special in that no `<li>`
tag is rendered and the name is never output:

    <ul class="menu">
      <li class="first">
        <a href="/">overview</a>
      </li>
      <li class="current last">
        <a href="/comments">comments</a>
      </li>
    </ul>

As you can see, the name "My menu" appears nowhere. The root menu item
will always render its children, but not itself. However, any attributes
that you set on your root will be output on the top-level `<ul`> element
itself.

To facilitate the creation of the root node, a special helper class, `Bundle\MenuBundle\Menu`
was created:

    use Bundle\MenuBundle\Menu;

    $menu = new Menu(array('id' => 'root_menu');
    $menu->addChild('overview', '@homepage');
    $menu->addChild('comments', '@comments');

This will create the same menu as the previous option, but allows you to
skip the specification of a name or route (the first and only argument
is the array of attributes for the `<ul`>) for the root node.

--->To continue reading, see Chapter 2: Customizing Menu Items
