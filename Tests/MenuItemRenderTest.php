<?php

namespace Knplabs\MenuBundle\Tests;
use Knplabs\MenuBundle\MenuItem;

class MenuItemRenderTest extends \PHPUnit_Framework_TestCase
{
    public function testRenderEmptyRoot()
    {
        $menu = new MenuItem('test');
        $menu->getRenderer()->setRenderCompressed(true);
        $rendered = '';
        $this->assertEquals($rendered, $menu->render());
    }

    public function testRenderRootWithAttributes()
    {
        $menu = new MenuItem('test', null, array('class' => 'test_class'));
        $menu->getRenderer()->setRenderCompressed(true);
        $menu->addChild('c1');
        $rendered = '<ul class="test_class"><li class="first last"><span>c1</span></li></ul>';
        $this->assertEquals($rendered, $menu->render());
    }

    public function testRenderEncodedAttributes()
    {
        $menu = new MenuItem('test', null, array('title' => 'encode " me >'));
        $menu->getRenderer()->setRenderCompressed(true);
        $menu->addChild('c1');
        $rendered = '<ul title="encode &quot; me &gt;"><li class="first last"><span>c1</span></li></ul>';
        $this->assertEquals($rendered, $menu->render());
    }

    public function testRenderLink()
    {
        extract($this->getSampleTree());
        $about = $menu->addChild('About', '/about');

        $rendered = '<a href="/about">About</a>';
        $this->assertEquals($rendered, $about->renderLink());

        $rendered = '<li class="last"><a href="/about">About</a></li>';
        $this->assertEquals($rendered, $menu->getRenderer()->renderItem($about));
    }
    
    public function testRenderLinkWithAttributes()
    {
        extract($this->getSampleTree());
        $about = $menu->addChild('About', '/about');
        $about->setLinkAttribute('title', 'About page');
        
        $rendered = '<li class="last"><a href="/about" title="About page">About</a></li>';
        $this->assertEquals($rendered, $menu->getRenderer()->renderItem($about));
    }

    public function testRenderWeirdLink()
    {
        extract($this->getSampleTree());
        $about = $menu->addChild('About', 'http://en.wikipedia.org/wiki/%22Weird_Al%22_Yankovic?v1=1&v2=2');

        $rendered = '<a href="http://en.wikipedia.org/wiki/%22Weird_Al%22_Yankovic?v1=1&v2=2">About</a>';
        $this->assertEquals($rendered, $about->renderLink());

        $rendered = '<li class="last"><a href="http://en.wikipedia.org/wiki/%22Weird_Al%22_Yankovic?v1=1&v2=2">About</a></li>';
        $this->assertEquals($rendered, $menu->getRenderer()->renderItem($about));
    }

    public function testRenderWholeMenu()
    {
        extract($this->getSampleTree());
        $rendered = '<ul class="root"><li class="first"><span>Parent 1</span><ul class="menu_level_1"><li class="first"><span>Child 1</span></li><li><span>Child 2</span></li><li class="last"><span>Child 3</span></li></ul></li><li class="last"><span>Parent 2</span><ul class="menu_level_1"><li class="first last"><span>Child 4</span><ul class="menu_level_2"><li class="first last"><span>Grandchild 1</span></li></ul></li></ul></li></ul>';
        $this->assertEquals($rendered, $menu->render());
    }

    public function testToString()
    {
        extract($this->getSampleTree());
        $rendered = '<ul class="root"><li class="first"><span>Parent 1</span><ul class="menu_level_1"><li class="first"><span>Child 1</span></li><li><span>Child 2</span></li><li class="last"><span>Child 3</span></li></ul></li><li class="last"><span>Parent 2</span><ul class="menu_level_1"><li class="first last"><span>Child 4</span><ul class="menu_level_2"><li class="first last"><span>Grandchild 1</span></li></ul></li></ul></li></ul>';
        $this->assertEquals($rendered, (string) $menu);
    }

    public function testRenderWithClassAndTitle()
    {
        extract($this->getSampleTree());
        $pt2->setAttribute('class', 'parent2_class');
        $pt2->setAttribute('title', 'parent2 title');
        $rendered = '<ul class="root"><li class="first"><span>Parent 1</span><ul class="menu_level_1"><li class="first"><span>Child 1</span></li><li><span>Child 2</span></li><li class="last"><span>Child 3</span></li></ul></li><li class="parent2_class last" title="parent2 title"><span>Parent 2</span><ul class="menu_level_1"><li class="first last"><span>Child 4</span><ul class="menu_level_2"><li class="first last"><span>Grandchild 1</span></li></ul></li></ul></li></ul>';
        $this->assertEquals($rendered, $menu->render());
    }

    public function testRenderWithCurrentItem()
    {
        extract($this->getSampleTree());
        $ch2->setIsCurrent(true);
        $rendered = '<ul class="root"><li class="current_ancestor first"><span>Parent 1</span><ul class="menu_level_1"><li class="first"><span>Child 1</span></li><li class="current"><span>Child 2</span></li><li class="last"><span>Child 3</span></li></ul></li><li class="last"><span>Parent 2</span><ul class="menu_level_1"><li class="first last"><span>Child 4</span><ul class="menu_level_2"><li class="first last"><span>Grandchild 1</span></li></ul></li></ul></li></ul>';
        $this->assertEquals($rendered, $menu->render());
    }
    
    public function testRenderWithCurrentItemAsLink()
    {
        extract($this->getSampleTree());
        $about = $menu->addChild('About', '/about');
        $about->setIsCurrent(true);
        $menu->setCurrentAsLink(true);
        
        $rendered = '<li class="current last"><a href="/about">About</a></li>';
        $this->assertEquals($rendered, $menu->getRenderer()->renderItem($about));
    }
    
    public function testRenderWithCurrentItemNotAsLink()
    {
        extract($this->getSampleTree());
        $about = $menu->addChild('About', '/about');
        $about->setIsCurrent(true);
        $menu->setCurrentAsLink(false);
        
        $rendered = '<li class="current last"><span>About</span></li>';
        $this->assertEquals($rendered, $menu->getRenderer()->renderItem($about));
    }

    public function testRenderSubMenuPortionWithClassAndTitle()
    {
        extract($this->getSampleTree());
        $pt2->setAttribute('class', 'parent2_class');
        $pt2->setAttribute('title', 'parent2 title');
        $rendered = '<ul class="parent2_class" title="parent2 title"><li class="first last"><span>Child 4</span><ul class="menu_level_2"><li class="first last"><span>Grandchild 1</span></li></ul></li></ul>';
        $this->assertEquals($rendered, $menu['Parent 2']->render());
    }

    public function testDoNotShowChildrenRendersNothing()
    {
        extract($this->getSampleTree());
        $menu->setShowChildren(false);
        $rendered = '';
        $this->assertEquals($rendered, $menu->render());
    }

    public function testDoNotShowChildChildrenRendersPartialMenu()
    {
        extract($this->getSampleTree());
        $menu['Parent 1']->setShowChildren(false);
        $rendered = '<ul class="root"><li class="first"><span>Parent 1</span></li><li class="last"><span>Parent 2</span><ul class="menu_level_1"><li class="first last"><span>Child 4</span><ul class="menu_level_2"><li class="first last"><span>Grandchild 1</span></li></ul></li></ul></li></ul>';
        $this->assertEquals($rendered, $menu->render());
    }

    public function testDoNotShowChildRendersPartialMenu()
    {
        extract($this->getSampleTree());
        $menu['Parent 1']->setShow(false);
        $rendered = '<ul class="root"><li class="first last"><span>Parent 2</span><ul class="menu_level_1"><li class="first last"><span>Child 4</span><ul class="menu_level_2"><li class="first last"><span>Grandchild 1</span></li></ul></li></ul></li></ul>';
        $this->assertEquals($rendered, $menu->render());
    }

    public function testDepth0()
    {
        extract($this->getSampleTree());
        $rendered = '';
        $this->assertEquals($rendered, $menu->render(0));
    }

    public function testDepth1()
    {
        extract($this->getSampleTree());
        $rendered = '<ul class="root"><li class="first"><span>Parent 1</span></li><li class="last"><span>Parent 2</span></li></ul>';
        $this->assertEquals($rendered, $menu->render(1));
    }

    public function testDepth2()
    {
        extract($this->getSampleTree());
        $rendered = '<ul class="root"><li class="first"><span>Parent 1</span><ul class="menu_level_1"><li class="first"><span>Child 1</span></li><li><span>Child 2</span></li><li class="last"><span>Child 3</span></li></ul></li><li class="last"><span>Parent 2</span><ul class="menu_level_1"><li class="first last"><span>Child 4</span></li></ul></li></ul>';
        $this->assertEquals($rendered, $menu->render(2));
    }

    public function testDepth2WithNotShowChildChildren()
    {
        extract($this->getSampleTree());
        $menu['Parent 1']->setShowChildren(false);
        $rendered = '<ul class="root"><li class="first"><span>Parent 1</span></li><li class="last"><span>Parent 2</span><ul class="menu_level_1"><li class="first last"><span>Child 4</span></li></ul></li></ul>';
        $this->assertEquals($rendered, $menu->render(2));
    }

    public function testReordering()
    {
        $menu = new MenuItem('root');
        $menu->getRenderer()->setRenderCompressed(true);
        $menu->addChild('c1');
        $menu->addChild('c2');
        $menu->addChild('c3');
        $menu->addChild('c4');

        $menu['c3']->moveToFirstPosition();
        $arr = array_keys($menu->getChildren());
        $this->assertEquals(array('c3', 'c1', 'c2', 'c4'), $arr);

        $menu['c2']->moveToLastPosition();
        $arr = array_keys($menu->getChildren());
        $this->assertEquals(array('c3', 'c1', 'c4', 'c2'), $arr);

        $menu['c1']->moveToPosition(2);
        $arr = array_keys($menu->getChildren());
        $this->assertEquals(array('c3', 'c4', 'c1', 'c2'), $arr);

        $menu->reorderChildren(array('c4', 'c3', 'c2', 'c1'));
        $arr = array_keys($menu->getChildren());
        $this->assertEquals(array('c4', 'c3', 'c2', 'c1'), $arr);

        $this->assertEquals('<ul><li class="first"><span>c4</span></li><li><span>c3</span></li><li><span>c2</span></li><li class="last"><span>c1</span></li></ul>', $menu->render());
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testReorderingWithTooManyItemNames()
    {
        $menu = new MenuItem('root');
        $menu->addChild('c1');
        $menu->reorderChildren(array('c1', 'c3'));
    }

    /**
     * @return array the tree items
     */
    protected function getSampleTree($class = 'Knplabs\MenuBundle\MenuItem')
    {
        $menu = new $class('Root li', null, array('class' => 'root'));
        $menu->getRenderer()->setRenderCompressed(true);
        $pt1 = $menu->addChild('Parent 1');
        $ch1 = $pt1->addChild('Child 1');
        $ch2 = $pt1->addChild('Child 2');

        // add the 3rd child via addChild with an object
        $ch3 = new $class('Child 3');
        $pt1->addChild($ch3);

        $pt2 = $menu->addChild('Parent 2');
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
