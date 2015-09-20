<?php

namespace Knp\Bundle\MenuBundle\Tests\Factory;

use Knp\Bundle\MenuBundle\Factory\ExpressionExtension;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class ExpressionExtensionTest extends \PHPUnit_Framework_TestCase
{
    public function testBuildOptions()
    {
        $context = $this->getMock('Knp\Bundle\MenuBundle\Expression\ExpressionContextInterface');
        $context->expects($this->exactly(2))->method('evaluate')->willReturn('evaluated expression');

        $extension = new ExpressionExtension($context);

        $options = $extension->buildOptions(array('foo' => 'bar', array('baz' => 'foo')));

        $this->assertSame(array('foo' => 'evaluated expression', array('baz' => 'evaluated expression')), $options);
    }
}
