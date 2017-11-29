Disabling the Core Menu Providers
=================================

To be able to use different menu providers together (the builder-service-based
one, the container-based one and the convention-based one for instance),
a chain provider is used. However, it is not used when only one provider
is enabled to increase performance by getting rid of the wrapping. If you
don't want to use the built-in providers, you can disable them through the
configuration:

.. code-block:: yaml

    #app/config/config.yml
    knp_menu:
        providers:
            builder_alias: false    # disable the builder-alias-based provider
            builder_service: false
            container_aware: true   # keep this one enabled. Can be omitted as it is the default

.. note::

    All providers are enabled by default.
