Registering your own renderer
=============================

Registering your own renderer in the renderer provider is simply a matter
of creating a service tagged with ``knp_menu.renderer``:

.. code-block:: yaml

    # app/config/services.yml
    services:
        app.menu_renderer:
            # The class implements Knp\Menu\Renderer\RendererInterface
            class: AppBundle\Menu\CustomRenderer
            arguments: ["%kernel.charset%"] # set your own dependencies here
            tags:
                # The alias is what is used to retrieve the menu
                - { name: knp_menu.renderer, alias: custom }

        # ...

If your renderer extends ``ListRenderer``, you need to provide a ``Matcher`` instance.
The configuration is then the following:

.. code-block:: yaml

    # app/config/services.yml
    services:
        app.menu_renderer:
            # The class implements Knp\Menu\Renderer\RendererInterface
            class: AppBundle\Menu\CustomRenderer
            arguments:
                - @knp_menu.matcher
                - "%knp_menu.renderer.list.options%"
                - "%kernel.charset%"
                # add your own dependencies here
            tags:
                # The alias is what is used to retrieve the menu
                - { name: knp_menu.renderer, alias: custom }

        # ...

.. note::

    The renderer service must be public as it will be retrieved at runtime to
    keep it lazy-loaded.

You can now use your renderer to render your menu:

.. code-block:: html+jinja

    {{ knp_menu_render('main', {}, 'custom') }}

.. note::

    As the renderer is responsible to render some HTML code, the ``knp_menu_render``
    function is marked as safe. Take care to handle escaping data in your renderer
    to avoid XSS if you use some user input in the menu.
