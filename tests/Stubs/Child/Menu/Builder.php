<?php

namespace Knp\Bundle\MenuBundle\Tests\Stubs\Child\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;

class Builder
{
    public function mainMenu(FactoryInterface $factory): ItemInterface
    {
        return $factory->createItem('Main menu for the child');
    }
}
