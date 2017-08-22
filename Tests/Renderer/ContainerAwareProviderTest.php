<?php

namespace Knp\Bundle\MenuBundle\Tests\Renderer;

use Knp\Bundle\MenuBundle\Renderer\ContainerAwareProvider;
use PHPUnit\Framework\TestCase;

class ContainerAwareProviderTest extends TestCase
{
    public function testHas()
    {
        $provider = new ContainerAwareProvider(
            $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerInterface')->getMock(),
            'first',
            array('first' => 'first', 'second' => 'dummy')
        );
        $this->assertTrue($provider->has('first'));
        $this->assertTrue($provider->has('second'));
        $this->assertFalse($provider->has('third'));
    }

    public function testGetExistentRenderer()
    {
        $renderer = $this->getMockBuilder('Knp\Menu\Renderer\RendererInterface')->getMock();
        $container = $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerInterface')->getMock();
        $container->expects($this->once())
            ->method('get')
            ->with('renderer')
            ->will($this->returnValue($renderer))
        ;
        $provider = new ContainerAwareProvider($container, 'custom', array('default' => 'renderer', 'custom' => 'other'));
        $this->assertSame($renderer, $provider->get('default'));
    }

    public function testGetDefaultRenderer()
    {
        $renderer = $this->getMockBuilder('Knp\Menu\Renderer\RendererInterface')->getMock();
        $container = $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerInterface')->getMock();
        $container->expects($this->once())
            ->method('get')
            ->with('renderer')
            ->will($this->returnValue($renderer))
        ;
        $provider = new ContainerAwareProvider($container, 'default', array('default' => 'renderer'));
        $this->assertSame($renderer, $provider->get());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetNonExistentRenderer()
    {
        $provider = new ContainerAwareProvider($this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerInterface')->getMock(), 'default', array());
        $provider->get('non-existent');
    }
}
