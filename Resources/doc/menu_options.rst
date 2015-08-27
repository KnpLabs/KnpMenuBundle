Setting Default Menu Options
============================

When creating a menu, menu options are set by passing them to ``addChild()``.
When all menu items need the same option, adding the same options to each child
can be cumbersome.

In these cases, you can configure the menu options in the configuration file:

.. configuration-block::

    .. code-block:: yaml

        # app/config/config.yml
        knp_menu:
            default_menu_options:
                class: menu-item

    .. code-block:: xml

        <!-- app/config/config.xml -->
        <?xml version="1.0" encoding="UTF-8" ?>
        <container xmlns="http://symfony.com/schema/dic/services"
            xmlns:xsi="http://www.w3.org/2001/XMLSchema-Instance"
            xsi:schemaLocation="http://symfony.com/schema/dic/services
                http://symfony.com/schema/dic/services/services-1.0.xsd">

            <config xmlns="http://knplabs.com/schema/dic/menu">
                <default-menu-option name="class">menu-item</default-menu-option>
            </config>
        </container>

    .. code-block:: php

        // app/config/config.php
        $container->loadFromExtension('knp_menu', array(
            'default_menu_options' => array(
                'class' => 'menu-item',
            ),
        ));

These options are overriden by options that are explicitely passed to the menu item::

    // ...
    $menu->addChild('Home', array('class' => 'menu-item menu-item-home'));
