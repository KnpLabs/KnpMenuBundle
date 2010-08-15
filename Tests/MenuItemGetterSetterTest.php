<?php

namespace Bundle\MenuBundle\Tests;
use Bundle\MenuBundle\MenuItem;

class MenuItemGetterSetterTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateMenuItemWithEmptyParameter()
    {
        $menu = $this->createMenu();
        $this->assertTrue($menu instanceof MenuItem);
    }

    public function testCreateMenuWithNameAndUri()
    {
        $menu = $this->createMenu('test1', 'other_uri');
        $this->assertEquals('test1', $menu->getName());
        $this->assertEquals('other_uri', $menu->getUri());
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

    public function testUri()
    {
        $menu = $this->createMenu();
        $menu->setUri('menu_uri');
        $this->assertEquals('menu_uri', $menu->getUri());
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

    public function testNum()
    {
        $menu = $this->createMenu();
        $this->assertEquals(null, $menu->getNum());
        $menu->setNum(3);
        $this->assertEquals(3, $menu->getNum());
    }

    public function testRenderLabel()
    {
        $menu = $this->createMenu('My Label');
        $this->assertEquals('My Label', $menu->renderLabel());
        $menu->setLabel('Other Label');
        $this->assertEquals('Other Label', $menu->renderLabel());
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testSetExistingNameThrowsAnException()
    {
        $menu = $this->createMenu();
        $menu->addChild('jack');
        $menu->addChild('joe');
        $menu->getChild('joe')->setName('jack');
    }
        
    /**
     * Create a new MenuItem 
     * 
     * @param string $name 
     * @param string $uri 
     * @param array $attributes 
     * @return MenuItem
     */
    protected function createMenu($name = 'test_menu', $uri = 'homepage', array $attributes = array())
    {
        return new MenuItem($name, $uri, $attributes);
    }
}
