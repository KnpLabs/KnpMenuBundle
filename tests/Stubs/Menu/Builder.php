<?php

namespace Knp\Bundle\MenuBundle\Tests\Stubs\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;

class Builder
{
    public function mainMenu(FactoryInterface $factory): ItemInterface
    {
        return $factory->createItem('Main menu');
    }

    public function invalidMethod(FactoryInterface $factory): \stdClass
    {
        return new \stdClass();
    }
}
