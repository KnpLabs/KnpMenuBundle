<?php

namespace Knp\Bundle\MenuBundle\Tests\Stubs\Child\Menu;

use Knp\Menu\FactoryInterface;

class Builder
{
    public function mainMenu(FactoryInterface $factory)
    {
        return $factory->createItem('Main menu for the child');
    }
}