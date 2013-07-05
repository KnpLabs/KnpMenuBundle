<?php

namespace Knp\Bundle\MenuBundle\Tests\Stubs\Menu;

use Knp\Menu\FactoryInterface;

class Builder
{
    public function mainMenu(FactoryInterface $factory)
    {
        return $factory->createItem('Main menu');
    }

    public function invalidMethod(FactoryInterface $factory)
    {
        return new \stdClass();
    }
}
