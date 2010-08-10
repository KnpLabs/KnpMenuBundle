<?php

namespace Bundle\MenuBundle\Tests;
use Bundle\MenuBundle\MenuItem;

class TestMenuItem extends MenuItem {}

class MenuItemTreeTest extends \PHPUnit_Framework_TestCase
{

    public function testSampleTreeIntegrity()
    {
        extract($this->getSampleTree());

        $this->assertEquals(2, count($menu));
        $this->assertEquals(3, count($menu['Parent 1']));
        $this->assertEquals(1, count($menu['Parent 2']));
        $this->assertEquals(1, count($menu['Parent 2']['Child 4']));
        $this->assertEquals('Grandchild 1', $menu['Parent 2']['Child 4']['Grandchild 1']->getName());
    }

    public function testChildrenHaveParentClass()
    {
        $class = 'Bundle\MenuBundle\Tests\TestMenuItem';
        extract($this->getSampleTree($class));

        $this->assertTrue($pt1 instanceof $class);
        $this->assertTrue($ch1 instanceof $class);
    } 

    public function testHierarchyGetLevel()
    {
        extract($this->getSampleTree());
        $this->assertEquals(0, $menu->getLevel());
        $this->assertEquals(1, $pt1->getLevel());
        $this->assertEquals(1, $pt2->getLevel());
        $this->assertEquals(2, $ch4->getLevel());
        $this->assertEquals(3, $gc1->getLevel());
    }

    public function testHierarchyGetRoot()
    {
        extract($this->getSampleTree());
        $this->assertEquals($menu, $menu->getRoot());
        $this->assertEquals($menu, $pt1->getRoot());
        $this->assertEquals($menu, $gc1->getRoot());
    }

    public function testHierarchyIsRoot()
    {
        extract($this->getSampleTree());
        $this->assertTrue($menu->isRoot());
        $this->assertFalse($pt1->isRoot());
        $this->assertFalse($ch3->isRoot());
    }

    public function testHierarchyGetParent()
    {
        extract($this->getSampleTree());
        $this->assertEquals(null, $menu->getParent());
        $this->assertEquals($menu, $pt1->getParent());
        $this->assertEquals($ch4, $gc1->getParent());
    }

    public function testMoveSampleMenuToNewRoot()
    {
        extract($this->getSampleTree());
        $newRoot = new TestMenuItem("newRoot");
        $newRoot->addChild($menu);

        $this->assertEquals(1, $menu->getLevel());
        $this->assertEquals(2, $pt1->getLevel());

        $this->assertEquals($newRoot, $menu->getRoot());
        $this->assertEquals($newRoot, $pt1->getRoot());
        $this->assertFalse($menu->isRoot());
        $this->assertTrue($newRoot->isRoot());
        $this->assertEquals($newRoot, $menu->getParent());
    }


    /**
     * @return array the tree items
     */
    protected function getSampleTree($class = 'Bundle\MenuBundle\MenuItem')
    {
        $menu = new $class('Root li', null, array('class' => 'root'));
        $pt1 = $menu->getChild('Parent 1');
        $ch1 = $pt1->addChild('Child 1');
        $ch2 = $pt1->addChild('Child 2');

        // add the 3rd child via addChild with an object
        $ch3 = new $class('Child 3');
        $pt1->addChild($ch3);

        $pt2 = $menu->getChild('Parent 2');
        $ch4 = $pt2->addChild('Child 4');
        $gc1 = $ch4->addChild('Grandchild 1');

        $items = array(
            'menu'  => $menu,
            'pt1'   => $pt1,
            'pt2'   => $pt2,
            'ch1'   => $ch1,
            'ch2'   => $ch2,
            'ch3'   => $ch3,
            'ch4'   => $ch4,
            'gc1'   => $gc1,
        );

        return $items;
    }

    // prints a visual representation of our basic testing tree
    protected function printTestTree()
    {
        print('      Menu Structure   '."\n");
        print('               rt      '."\n");
        print('             /    \    '."\n");
        print('          pt1      pt2 '."\n");
        print('        /  | \      |  '."\n");
        print('      ch1 ch2 ch3  ch4 '."\n");
        print('                    |  '."\n");
        print('                   gc1 '."\n");
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
