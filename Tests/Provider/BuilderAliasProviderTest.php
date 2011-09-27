<?php

namespace Knp\Bundle\MenuBundle\Tests\Provider;

use Knp\Bundle\MenuBundle\Provider\BuilderAliasProvider;

class BuilderAliasProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testHas()
    {
        $provider = new BuilderAliasProvider(
            $this->getMock('Symfony\Component\HttpKernel\KernelInterface'),
            $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface'),
            $this->getMock('Knp\Menu\FactoryInterface')
        );
        $this->assertFalse($provider->has('foo'));
        $this->assertFalse($provider->has('foo:bar'));
        $this->assertTrue($provider->has('foo:bar:baz'));
    }

    public function testGetExistentMenu()
    {
        // mock the factory to return a set value when the builder creates the menu
        $factory = $this->getMock('Knp\Menu\FactoryInterface');
        $factory->expects($this->once())
            ->method('createItem')
            ->with('Main menu')
            ->will($this->returnValue($this->getMock('Knp\Menu\ItemInterface')));

        $provider = new BuilderAliasProvider(
            $this->createMockKernelForStub(),
            $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface'),
            $factory
        );

        $menu = $provider->get('FooBundle:Builder:mainMenu');
        // returns the mocked value returned from mocked factory
        $this->assertInstanceOf('Knp\Menu\ItemInterface', $menu);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testGetNonExistentMenuClass()
    {
        $provider = new BuilderAliasProvider(
            $this->createMockKernelForStub(),
            $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface'),
            $this->getMock('Knp\Menu\FactoryInterface')
        );

        $provider->get('FooBundle:Fake:mainMenu');
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testGetNonExistentMenuMethod()
    {
        $provider = new BuilderAliasProvider(
            $this->createMockKernelForStub(),
            $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface'),
            $this->getMock('Knp\Menu\FactoryInterface')
        );

        // bundle will return a null namespace, class won't be found
        $provider->get('FooBundle:Builder:fakeMenu');
    }

    /**
     * Returns a mocked kernel with a mocked "FooBundle" whose namespace
     * points to the Stubs directory.
     */
    private function createMockKernelForStub()
    {
        $bundle = $this->getMock('Symfony\Component\HttpKernel\Bundle\BundleInterface');
        $bundle->expects($this->once())
            ->method('getNamespace')
            ->will($this->returnValue('Knp\Bundle\MenuBundle\Tests\Stubs'))
        ;

        $kernel = $this->getMock('Symfony\Component\HttpKernel\KernelInterface');
        $kernel->expects($this->once())
            ->method('getBundle')
            ->with('FooBundle')
            ->will($this->returnValue($bundle))
        ;

        return $kernel;
    }
}