<?php

namespace Knp\Bundle\MenuBundle\Tests\Templating;

use Knp\Bundle\MenuBundle\Templating\Helper\MenuHelper;

/**
 * Test for MenuHelper class
 *
 * @author Leszek Prabucki <leszek.prabucki@gmail.com>
 */
class MenuHelperTest extends \PHPUnit_Framework_TestCase
{
    public function testGet()
    {
        $helperMock = $this->getHelperMock();
        $helperMock->expects($this->any())
            ->method('get')
            ->with($this->equalTo('test'), $this->equalTo(array('pathArray')))
            ->will($this->returnValue('returned value'));

        $helper = new MenuHelper($helperMock, $this->getMatcherMock(), $this->getManipulatorMock());

        $this->assertEquals('returned value', $helper->get('test', array('pathArray')));
    }

    public function testGetMenuWithOptions()
    {
        $menu = $this->getMock('Knp\Menu\ItemInterface');

        $helperMock = $this->getHelperMock();
        $helperMock->expects($this->any())
            ->method('get')
            ->with('default', array(), array('foo' => 'bar'))
            ->will($this->returnValue($menu))
        ;

        $helper = new MenuHelper($helperMock, $this->getMatcherMock(), $this->getManipulatorMock());

        $this->assertSame($menu, $helper->get('default', array(), array('foo' => 'bar')));
    }

    public function testRender()
    {
        $helperMock = $this->getHelperMock();
        $helperMock->expects($this->any())
            ->method('render')
            ->with($this->equalTo('test'), $this->equalTo(array('options')))
            ->will($this->returnValue('returned value'));

        $helper = new MenuHelper($helperMock, $this->getMatcherMock(), $this->getManipulatorMock());

        $this->assertEquals('returned value', $helper->render('test', array('options')));
    }

    public function testGetName()
    {
        $helper = new MenuHelper($this->getHelperMock(), $this->getMatcherMock(), $this->getManipulatorMock());

        $this->assertEquals('knp_menu', $helper->getName());
    }

    public function testGetBreadcrumbsArray()
    {
        $helperMock = $this->getHelperMock(array('getBreadcrumbsArray'));
        $helperMock->expects($this->any())
            ->method('getBreadcrumbsArray')
            ->with('default')
            ->will($this->returnValue(array('A', 'B')))
        ;

        $helper = new MenuHelper($helperMock, $this->getMatcherMock(), $this->getManipulatorMock());

        $this->assertEquals(array('A', 'B'), $helper->getBreadcrumbsArray('default'));
    }

    public function testPathAsString()
    {
        $menu = $this->getMock('Knp\Menu\ItemInterface');

        $manipulatorMock = $this->getManipulatorMock(array('getPathAsString'));
        $manipulatorMock->expects($this->any())
            ->method('getPathAsString')
            ->with($menu)
            ->will($this->returnValue('A > B'))
        ;

        $helper = new MenuHelper($this->getHelperMock(), $this->getMatcherMock(), $manipulatorMock);

        $this->assertEquals('A > B', $helper->getPathAsString($menu));
    }

    public function testIsCurrent()
    {
        $current = $this->getMock('Knp\Menu\ItemInterface');
        $notCurrent = $this->getMock('Knp\Menu\ItemInterface');

        $matcherMock = $this->getMatcherMock();
        $matcherMock->expects($this->any())
            ->method('isCurrent')
            ->withConsecutive(array($current), array($notCurrent))
            ->will($this->onConsecutiveCalls(true, false))
        ;

        $helper = new MenuHelper($this->getHelperMock(), $matcherMock, $this->getManipulatorMock());

        $this->assertTrue($helper->isCurrent($current));
        $this->assertFalse($helper->isCurrent($notCurrent));
    }

    public function testIsAncestor()
    {
        $menu = $this->getMock('Knp\Menu\ItemInterface');

        $matcherMock = $this->getMatcherMock();
        $matcherMock->expects($this->any())
            ->method('isAncestor')
            ->with($menu)
            ->will($this->returnValue(false))
        ;

        $helper = new MenuHelper($this->getHelperMock(), $matcherMock, $this->getManipulatorMock());

        $this->assertFalse($helper->isAncestor($menu));
    }

    public function testGetCurrentItem()
    {
        $menu = $this->getMock('Knp\Menu\ItemInterface');

        $helperMock = $this->getHelperMock(array('getCurrentItem'));
        $helperMock->expects($this->any())
            ->method('getCurrentItem')
            ->with('default')
            ->will($this->returnValue($menu))
        ;

        $helper = new MenuHelper($helperMock, $this->getMatcherMock(), $this->getManipulatorMock());

        $this->assertEquals($menu, $helper->getCurrentItem('default'));
    }

    private function getHelperMock(array $methods = array())
    {
        return $this->getMockBuilder('Knp\Menu\Twig\Helper')
            ->setMethods($methods)
            ->disableOriginalConstructor()
            ->getMock();
    }

    private function getMatcherMock()
    {
        return $this->getMock('Knp\Menu\Matcher\MatcherInterface');
    }

    private function getManipulatorMock(array $methods = array())
    {
        return $this->getMockBuilder('Knp\Menu\Util\MenuManipulator')
            ->setMethods($methods)
            ->getMock();
    }
}
