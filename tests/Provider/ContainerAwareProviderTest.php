<?php

namespace Knp\Bundle\MenuBundle\Tests\Provider;

use Knp\Bundle\MenuBundle\Provider\ContainerAwareProvider;
use PHPUnit\Framework\TestCase;

class ContainerAwareProviderTest extends TestCase
{
    public function testHas()
    {
        $provider = new ContainerAwareProvider($this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerInterface')->getMock(), ['first' => 'first', 'second' => 'dummy']);
        $this->assertTrue($provider->has('first'));
        $this->assertTrue($provider->has('second'));
        $this->assertFalse($provider->has('third'));
    }

    public function testGetExistentMenu()
    {
        $menu = $this->getMockBuilder('Knp\Menu\ItemInterface')->getMock();
        $container = $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerInterface')->getMock();
        $container->expects($this->once())
            ->method('get')
            ->with('menu')
            ->will($this->returnValue($menu))
        ;
        $provider = new ContainerAwareProvider($container, ['default' => 'menu']);
        $this->assertSame($menu, $provider->get('default'));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetNonExistentMenu()
    {
        $provider = new ContainerAwareProvider($this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerInterface')->getMock());
        $provider->get('non-existent');
    }
}
