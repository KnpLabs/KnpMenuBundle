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
        $helperMock = $this->getMockBuilder('Knp\Menu\Twig\Helper')
            ->disableOriginalConstructor()
            ->getMock();
        $helperMock->expects($this->once())
            ->method('get')
            ->with($this->equalTo('test'), $this->equalTo(array('pathArray')))
            ->will($this->returnValue('returned value'));

        $helper = new MenuHelper($helperMock);

        $this->assertEquals('returned value', $helper->get('test', array('pathArray')));
    }

    public function testGetMenuWithOptions()
    {
        $menu = $this->getMock('Knp\Menu\ItemInterface');
        $helperMock = $this->getMockBuilder('Knp\Menu\Twig\Helper')
            ->disableOriginalConstructor()
            ->getMock();
        $helperMock->expects($this->once())
            ->method('get')
            ->with('default', array(), array('foo' => 'bar'))
            ->will($this->returnValue($menu))
        ;
        $helper = new MenuHelper($helperMock);
        $this->assertSame($menu, $helper->get('default', array(), array('foo' => 'bar')));
    }

    public function testRender()
    {
        $helperMock = $this->getMockBuilder('Knp\Menu\Twig\Helper')
            ->disableOriginalConstructor()
            ->getMock();
        $helperMock->expects($this->once())
            ->method('render')
            ->with($this->equalTo('test'), $this->equalTo(array('options')))
            ->will($this->returnValue('returned value'));

        $helper = new MenuHelper($helperMock);

        $this->assertEquals('returned value', $helper->render('test', array('options')));
    }

    public function testGetName()
    {
        $helperMock = $this->getMockBuilder('Knp\Menu\Twig\Helper')
            ->disableOriginalConstructor()
            ->getMock();

        $helper = new MenuHelper($helperMock);

        $this->assertEquals('knp_menu', $helper->getName());
    }
}
