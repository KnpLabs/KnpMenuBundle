<?php

namespace Bundle\MenuBundle\Tests;
use Bundle\MenuBundle\MenuItem;

class MenuItemTreeTest extends \PHPUnit_Framework_TestCase
{
    public function testSampleTree()
    {
        $class = 'Bundle\MenuBundle\MenuItem';
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

    /**
     * @depends testSampleTree
     */
    public function testSampleTreeIntegrity(array $items)
    {
        extract($items);

        $this->assertEquals(2, count($menu));
        $this->assertEquals(3, count($menu['Parent 1']));
        $this->assertEquals(1, count($menu['Parent 2']));
        $this->assertEquals(1, count($menu['Parent 2']['Child 4']));
        $this->assertEquals('Grandchild 1', $menu['Parent 2']['Child 4']['Grandchild 1']->getName());
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
