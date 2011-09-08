<?php

namespace Knp\Bundle\MenuBundle\Tests\Provider;

use Knp\Bundle\MenuBundle\Provider\ContainerAwareProvider;

class ContainerAwareProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testHas()
    {
        $provider = new ContainerAwareProvider($this->getMock('Symfony\Component\DependencyInjection\ContainerInterface'), array('first' => 'first', 'second' => 'dummy'));
        $this->assertTrue($provider->has('first'));
        $this->assertTrue($provider->has('second'));
        $this->assertFalse($provider->has('third'));
    }

    public function testGetExistentMenu()
    {
        $menu = $this->getMock('Knp\Menu\ItemInterface');
        $container = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');
        $container->expects($this->once())
            ->method('get')
            ->with('menu')
            ->will($this->returnValue($menu))
        ;
        $provider = new ContainerAwareProvider($container, array('default' => 'menu'));
        $this->assertSame($menu, $provider->get('default'));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testGetNonExistentMenu()
    {
        $provider = new ContainerAwareProvider($this->getMock('Symfony\Component\DependencyInjection\ContainerInterface'));
        $provider->get('non-existent');
    }
}
