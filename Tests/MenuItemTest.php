<?php

namespace Bundle\MenuBundle\Tests;
use Bundle\MenuBundle\MenuItem;

class MenuItemTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateMenuItemWithEmptyParameter()
    {
        $menu = $this->createMenu();
        $this->assertTrue($menu instanceof MenuItem);
    }

    public function testCreateMenuWithNameAndRoute()
    {
        $menu = $this->createMenu('test1', 'other_route');
        $this->assertEquals('test1', $menu->getName());
        $this->assertEquals('other_route', $menu->getRoute());
    }

    public function testCreateMenuWithTitle()
    {
        $title = 'This is a test item title';
        $menu = $this->createMenu(null, null, array('title' => $title));
        $this->assertEquals($title, $menu->getAttribute('title'));
    }

    public function testName()
    {
        $menu = $this->createMenu();
        $menu->setName('menu name');
        $this->assertEquals('menu name', $menu->getName());
    }

    public function testLabel()
    {
        $menu = $this->createMenu();
        $menu->setLabel('menu label');
        $this->assertEquals('menu label', $menu->getLabel());
    }

    public function testRoute()
    {
        $menu = $this->createMenu();
        $menu->setRoute('menu_route');
        $this->assertEquals('menu_route', $menu->getRoute());
    }

    public function testAttributes()
    {
        $attributes = array('class' => 'test_class', 'title' => 'Test title');
        $menu = $this->createMenu();
        $menu->setAttributes($attributes);
        $this->assertEquals($attributes, $menu->getAttributes());
    }

    public function testDefaultAttribute()
    {
        $menu = $this->createMenu(null, null, array('id' => 'test_id'));
        $this->assertEquals('test_id', $menu->getAttribute('id'));
        $this->assertEquals('default_value', $menu->getAttribute('unknown_attribute', 'default_value'));
    }

    public function testShow()
    {
        $menu = $this->createMenu();
        $this->assertEquals(true, $menu->getShow());
        $menu->setShow(false);
        $this->assertEquals(false, $menu->getShow());
    }

    public function testShowChildren()
    {
        $menu = $this->createMenu();
        $this->assertEquals(true, $menu->getShowChildren());
        $menu->setShowChildren(false);
        $this->assertEquals(false, $menu->getShowChildren());
    }

    public function testParent()
    {
        $menu = $this->createMenu();
        $child = $this->createMenu('child_menu');
        $this->assertNull($child->getParent());
        $child->setParent($menu);
        $this->assertEquals($menu, $child->getParent());
    }

    public function testChildren()
    {
        $menu = $this->createMenu();
        $child = $this->createMenu('child_menu');
        $menu->setChildren(array($child));
        $this->assertEquals(array($child), $menu->getChildren());
    }
        
    /**
     * Create a new MenuItem 
     * 
     * @param string $name 
     * @param string $route 
     * @param array $attributes 
     * @return MenuItem
     */
    protected function createMenu($name = 'test_menu', $route = 'homepage', array $attributes = array())
    {
        return new MenuItem($name, $route, $attributes);
    }
}
