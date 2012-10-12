Registering your own provider
=============================

Registering your own menu provider allows you to feed your menu with your own
data, accessed by your code. It can for example go through a PHPCR repository
and create the corresponding menu elements.

Create first your Provider class, in the Provider directory of your bundle:


```php
<?php

namespace Acme\DemoBundle\Provider;

use Symfony\Component\DependencyInjection\ContainerInterface;

use Knp\Menu\FactoryInterface;
use Knp\Menu\Provider\MenuProviderInterface;

class CustomMenuProvider implements MenuProviderInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var FactoryInterface
     */
    protected $factory = null;


    /**
     * @param ContainerInterface $container the container used to get the request
     * @param FactoryInterface $factory the menu factory used to create the menu item
     */
    public function __construct(ContainerInterface $container, FactoryInterface $factory)
    {
        $this->container = $container;
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
			$menuItem->setUri($this->container->get('request')->getRequestUri());

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

```	

Then, configure the services linked to this new provider. 

```yaml
services:
  acme_demo_menu.provider:
        class: Acme\DemoBundle\Provider\CustomMenuProvider
        arguments:
          - @service_container
          - @knp_menu.factory
        tags:
          - { name: knp_menu.provider }
```	

Finally, to generate the menu, for example inside a twig template type:

```jinja
{{ knp_menu_render('demo') }}
```

The Symfony CMF MenuBundle provides a complete working example: <https://github.com/symfony-cmf/MenuBundle>