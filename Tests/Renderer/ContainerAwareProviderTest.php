<?php

namespace Knp\Bundle\MenuBundle\Tests\Renderer;

use Knp\Bundle\MenuBundle\Renderer\ContainerAwareProvider;

class ContainerAwareProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testHas()
    {
        $provider = new ContainerAwareProvider($this->getMock('Symfony\Component\DependencyInjection\ContainerInterface'), array('first' => 'first', 'second' => 'dummy'));
        $this->assertTrue($provider->has('first'));
        $this->assertTrue($provider->has('second'));
        $this->assertFalse($provider->has('third'));
    }

    public function testGetExistentRenderer()
    {
        $renderer = $this->getMock('Knp\Menu\Renderer\RendererInterface');
        $container = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');
        $container->expects($this->once())
            ->method('get')
            ->with('renderer')
            ->will($this->returnValue($renderer))
        ;
        $provider = new ContainerAwareProvider($container, array('default' => 'renderer'));
        $this->assertSame($renderer, $provider->get('default'));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testGetNonExistentRenderer()
    {
        $provider = new ContainerAwareProvider($this->getMock('Symfony\Component\DependencyInjection\ContainerInterface'));
        $provider->get('non-existent');
    }
}
