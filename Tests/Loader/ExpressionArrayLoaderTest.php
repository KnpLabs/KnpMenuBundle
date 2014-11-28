<?php

namespace Knp\Bundle\MenuBundle\Tests\Loader;

use Knp\Bundle\MenuBundle\Loader\ExpressionArrayLoader;
use Knp\Menu\MenuFactory;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class ExpressionArrayLoaderTest extends \PHPUnit_Framework_TestCase
{
    public function testLoad()
    {
        $context = $this->getExpressionContextMock();
        $context->expects($this->exactly(4))->method('evaluate')->will($this->onConsecutiveCalls(true, true, false, false));

        $loader = new ExpressionArrayLoader(new MenuFactory(), $context);
        $menu = array('children' => array(
            'bar' => array('hide_if' => 'expression'),
            'foo' => array('show_if' => 'expression'),
            'baz' => array('show_if' => 'expression'),
            'boo' => array('hide_if' => 'expression'),
        ));

        $menu = $loader->load($menu);

        $this->assertInstanceOf('Knp\Menu\ItemInterface', $menu);

        $children = $menu->getChildren();

        $this->assertCount(2, $children);
        $this->assertArrayNotHasKey('bar', $children);
        $this->assertArrayHasKey('foo', $children);
        $this->assertInstanceOf('Knp\Menu\ItemInterface', $children['foo']);
        $this->assertArrayNotHasKey('baz', $children);
        $this->assertArrayHasKey('boo', $children);
        $this->assertInstanceOf('Knp\Menu\ItemInterface', $children['boo']);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testLoadInvalid()
    {
        $loader = new ExpressionArrayLoader(new MenuFactory(), $this->getExpressionContextMock());

        $loader->load('foo');
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Knp\Menu\FactoryInterface
     */
    private function getFactoryMock()
    {
        return $this->getMock('Knp\Menu\FactoryInterface');
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Knp\Bundle\MenuBundle\Expression\ExpressionContextInterface
     */
    private function getExpressionContextMock()
    {
        return $this->getMock('Knp\Bundle\MenuBundle\Expression\ExpressionContextInterface');
    }
}
