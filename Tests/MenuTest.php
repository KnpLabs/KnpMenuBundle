<?php

namespace Bundle\MenuBundle\Tests;
use Bundle\MenuBundle\Menu;

class MenuTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateMenuWithEmptyParameter()
    {
        $menu = new Menu();
        $this->assertTrue($menu instanceof Menu);
    }

    public function testCreateMenuWithAttributes()
    {
        $menu = new Menu(array('class' => 'root'));
        $this->assertEquals('root', $menu->getAttribute('class'));
    }

    public function testCreateMenuWithItemClass()
    {
        $childClass = 'Bundle\MenuBundle\OtherMenuItem';
        $menu = new Menu(null, $childClass);
        $this->assertEquals($childClass, $menu->getChildClass());
    }
}
