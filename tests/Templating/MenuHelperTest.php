<?php

namespace Knp\Bundle\MenuBundle\Tests\Templating;

use Knp\Bundle\MenuBundle\Templating\Helper\MenuHelper;
use Knp\Menu\ItemInterface;
use PHPUnit\Framework\TestCase;

/**
 * Test for MenuHelper class.
 *
 * @author Leszek Prabucki <leszek.prabucki@gmail.com>
 * @group legacy
 */
class MenuHelperTest extends TestCase
{
    public function testGet(): void
    {
        $itemMock = $this->createMock(ItemInterface::class);
        $helperMock = $this->getHelperMock(['get']);
        $helperMock
            ->method('get')
            ->with($this->equalTo('test'), $this->equalTo(['pathArray']))
            ->willReturn($itemMock);

        $helper = new MenuHelper($helperMock, $this->getMatcherMock(), $this->getManipulatorMock());

        $this->assertSame($itemMock, $helper->get('test', ['pathArray']));
    }

    public function testGetMenuWithOptions(): void
    {
        $menu = $this->getMockBuilder('Knp\Menu\ItemInterface')->getMock();

        $helperMock = $this->getHelperMock(['get']);
        $helperMock->expects($this->any())
            ->method('get')
            ->with('default', [], ['foo' => 'bar'])
            ->willReturn($menu)
        ;

        $helper = new MenuHelper($helperMock, $this->getMatcherMock(), $this->getManipulatorMock());

        $this->assertSame($menu, $helper->get('default', [], ['foo' => 'bar']));
    }

    public function testRender(): void
    {
        $helperMock = $this->getHelperMock(['render']);
        $helperMock->expects($this->any())
            ->method('render')
            ->with($this->equalTo('test'), $this->equalTo(['options']))
            ->willReturn('returned value');

        $helper = new MenuHelper($helperMock, $this->getMatcherMock(), $this->getManipulatorMock());

        $this->assertEquals('returned value', $helper->render('test', ['options']));
    }

    public function testGetName(): void
    {
        $helper = new MenuHelper($this->getHelperMock(), $this->getMatcherMock(), $this->getManipulatorMock());

        $this->assertEquals('knp_menu', $helper->getName());
    }

    public function testGetBreadcrumbsArray(): void
    {
        $helperMock = $this->getHelperMock(['getBreadcrumbsArray']);
        $helperMock->expects($this->any())
            ->method('getBreadcrumbsArray')
            ->with('default')
            ->willReturn(['A', 'B'])
        ;

        $helper = new MenuHelper($helperMock, $this->getMatcherMock(), $this->getManipulatorMock());

        $this->assertEquals(['A', 'B'], $helper->getBreadcrumbsArray('default'));
    }

    public function testPathAsString(): void
    {
        $menu = $this->getMockBuilder('Knp\Menu\ItemInterface')->getMock();

        $manipulatorMock = $this->getManipulatorMock(['getPathAsString']);
        $manipulatorMock->expects($this->any())
            ->method('getPathAsString')
            ->with($menu)
            ->willReturn('A > B')
        ;

        $helper = new MenuHelper($this->getHelperMock(), $this->getMatcherMock(), $manipulatorMock);

        $this->assertEquals('A > B', $helper->getPathAsString($menu));
    }

    public function testIsCurrent(): void
    {
        $current = $this->getMockBuilder('Knp\Menu\ItemInterface')->getMock();
        $notCurrent = $this->getMockBuilder('Knp\Menu\ItemInterface')->getMock();

        $matcherMock = $this->getMatcherMock();

        $matcherMock->expects($this->any())
            ->method('isCurrent')
            ->willReturnOnConsecutiveCalls(true, false)
        ;

        $helper = new MenuHelper($this->getHelperMock(), $matcherMock, $this->getManipulatorMock());

        $this->assertTrue($helper->isCurrent($current));
        $this->assertFalse($helper->isCurrent($notCurrent));
    }

    public function testIsAncestor(): void
    {
        $menu = $this->getMockBuilder('Knp\Menu\ItemInterface')->getMock();

        $matcherMock = $this->getMatcherMock();
        $matcherMock->expects($this->any())
            ->method('isAncestor')
            ->with($menu)
            ->willReturn(false)
        ;

        $helper = new MenuHelper($this->getHelperMock(), $matcherMock, $this->getManipulatorMock());

        $this->assertFalse($helper->isAncestor($menu));
    }

    public function testGetCurrentItem(): void
    {
        $menu = $this->getMockBuilder('Knp\Menu\ItemInterface')->getMock();

        $helperMock = $this->getHelperMock(['getCurrentItem']);
        $helperMock->expects($this->any())
            ->method('getCurrentItem')
            ->with('default')
            ->willReturn($menu)
        ;

        $helper = new MenuHelper($helperMock, $this->getMatcherMock(), $this->getManipulatorMock());

        $this->assertEquals($menu, $helper->getCurrentItem('default'));
    }

    private function getHelperMock(array $methods = [])
    {
        return $this->getMockBuilder('Knp\Menu\Twig\Helper')
            ->onlyMethods($methods)
            ->disableOriginalConstructor()
            ->getMock();
    }

    private function getMatcherMock()
    {
        return $this->getMockBuilder('Knp\Menu\Matcher\MatcherInterface')->getMock();
    }

    private function getManipulatorMock(array $methods = [])
    {
        return $this->getMockBuilder('Knp\Menu\Util\MenuManipulator')
            ->onlyMethods($methods)
            ->getMock();
    }
}
