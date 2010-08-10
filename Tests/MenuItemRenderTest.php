<?php

namespace Bundle\MenuBundle\Tests;
use Bundle\MenuBundle\MenuItem;

class MenuItemRenderTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        MenuItem::setRenderCompressed(true);
    }

    public function testRenderWholeMenu()
    {
        extract($this->getSampleTree());
        $rendered = '<ul class="root"><li class="first">Parent 1<ul class="menu_level_1"><li class="first">Child 1</li><li>Child 2</li><li class="last">Child 3</li></ul></li><li class="last">Parent 2<ul class="menu_level_1"><li class="first last">Child 4<ul class="menu_level_2"><li class="first last">Grandchild 1</li></ul></li></ul></li></ul>';
        $this->assertEquals($rendered, $menu->render());
    }

    public function testToString()
    {
        extract($this->getSampleTree());
        $rendered = '<ul class="root"><li class="first">Parent 1<ul class="menu_level_1"><li class="first">Child 1</li><li>Child 2</li><li class="last">Child 3</li></ul></li><li class="last">Parent 2<ul class="menu_level_1"><li class="first last">Child 4<ul class="menu_level_2"><li class="first last">Grandchild 1</li></ul></li></ul></li></ul>';
        $this->assertEquals($rendered, (string) $menu);
    }

    public function testRenderWithClassAndTitle()
    {
        extract($this->getSampleTree());
        $pt2->setAttribute('class', 'parent2_class');
        $pt2->setAttribute('title', 'parent2 title');
        $rendered = '<ul class="root"><li class="first">Parent 1<ul class="menu_level_1"><li class="first">Child 1</li><li>Child 2</li><li class="last">Child 3</li></ul></li><li class="parent2_class last" title="parent2 title">Parent 2<ul class="menu_level_1"><li class="first last">Child 4<ul class="menu_level_2"><li class="first last">Grandchild 1</li></ul></li></ul></li></ul>';
        $this->assertEquals($rendered, $menu->render());
    }

    public function testRenderWithCurrentItem()
    {
        extract($this->getSampleTree());
        $ch2->setIsCurrent(true);
        $rendered = '<ul class="root"><li class="current_ancestor first">Parent 1<ul class="menu_level_1"><li class="first">Child 1</li><li class="current">Child 2</li><li class="last">Child 3</li></ul></li><li class="last">Parent 2<ul class="menu_level_1"><li class="first last">Child 4<ul class="menu_level_2"><li class="first last">Grandchild 1</li></ul></li></ul></li></ul>';
        $this->assertEquals($rendered, $menu->render());
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
}
