<?php

namespace Knp\Bundle\MenuBundle\Tests\Stubs\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAware;

class ContainerAwareBuilder extends ContainerAware
{
    public function mainMenu(FactoryInterface $factory)
    {
        // Check that the container is really set.
        $this->container->get('test');

        return $factory->createItem('Main menu');
    }
}
