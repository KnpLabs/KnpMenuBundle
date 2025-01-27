<?php

namespace Knp\Bundle\MenuBundle\Tests\Stubs\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ContainerAwareBuilder implements ContainerAwareInterface
{
    private ContainerInterface $container;

    public function setContainer(?ContainerInterface $container = null): void
    {
        $this->container = $container;
    }

    public function mainMenu(FactoryInterface $factory): ItemInterface
    {
        // Check that the container is really set.
        $this->container->get('test');

        return $factory->createItem('Main menu');
    }
}
