<?php

namespace Knp\Bundle\MenuBundle\Tests\Provider;

use Knp\Bundle\MenuBundle\Provider\BuilderAliasProvider;
use Knp\Bundle\MenuBundle\Tests\Stubs\TestKernel;
use Symfony\Component\HttpKernel\Kernel;

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
        $item = $this->getMock('Knp\Menu\ItemInterface');
        // mock the factory to return a set value when the builder creates the menu
        $factory = $this->getMock('Knp\Menu\FactoryInterface');
        $factory->expects($this->once())
            ->method('createItem')
            ->with('Main menu')
            ->will($this->returnValue($item));

        $provider = new BuilderAliasProvider(
            $this->createMockKernelForStub(),
            $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface'),
            $factory
        );

        $menu = $provider->get('FooBundle:Builder:mainMenu');
        // returns the mocked value returned from mocked factory
        $this->assertSame($item, $menu);
    }

    public function testGetContainerAwareMenu()
    {
        $item = $this->getMock('Knp\Menu\ItemInterface');
        // mock the factory to return a set value when the builder creates the menu
        $factory = $this->getMock('Knp\Menu\FactoryInterface');
        $factory->expects($this->once())
            ->method('createItem')
            ->with('Main menu')
            ->will($this->returnValue($item));

        $container = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');
        $container->expects($this->once())
            ->method('get')
            ->with('test');

        $provider = new BuilderAliasProvider(
            $this->createMockKernelForStub(),
            $container,
            $factory
        );

        $menu = $provider->get('FooBundle:ContainerAwareBuilder:mainMenu');
        // returns the mocked value returned from mocked factory
        $this->assertSame($item, $menu);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testGetInvalidReturnValue()
    {
        $provider = new BuilderAliasProvider(
            $this->createMockKernelForStub(),
            $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface'),
            $this->getMock('Knp\Menu\FactoryInterface')
        );

        $menu = $provider->get('FooBundle:Builder:invalidMethod');
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testGetNonExistentMenu()
    {
        $provider = new BuilderAliasProvider(
            $this->getMock('Symfony\Component\HttpKernel\KernelInterface'),
            $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface'),
            $this->getMock('Knp\Menu\FactoryInterface')
        );
        $provider->get('non-existent');
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Class "Knp\Bundle\MenuBundle\Tests\Stubs\Menu\Fake" does not exist for menu builder "FooBundle:Fake".
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

    public function testBundleInheritanceParent()
    {
        $item = $this->getMock('Knp\Menu\ItemInterface');
        // mock the factory to return a set value when the builder creates the menu
        $factory = $this->getMock('Knp\Menu\FactoryInterface');
        $factory->expects($this->once())
            ->method('createItem')
            ->with('Main menu')
            ->will($this->returnValue($item));

        $provider = new BuilderAliasProvider(
            $this->createTestKernel(),
            $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface'),
            $factory
        );

        $menu = $provider->get('FooBundle:Builder:mainMenu');
        // returns the mocked value returned from mocked factory
        $this->assertSame($item, $menu);
    }

    public function testBundleInheritanceChild()
    {
        $item = $this->getMock('Knp\Menu\ItemInterface');
        // mock the factory to return a set value when the builder creates the menu
        $factory = $this->getMock('Knp\Menu\FactoryInterface');
        $factory->expects($this->once())
            ->method('createItem')
            ->with('Main menu for the child')
            ->will($this->returnValue($item));

        $provider = new BuilderAliasProvider(
            $this->createTestKernel('Knp\Bundle\MenuBundle\Tests\Stubs\Child'),
            $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface'),
            $factory
        );

        $menu = $provider->get('FooBundle:Builder:mainMenu');
        // returns the mocked value returned from mocked factory
        $this->assertSame($item, $menu);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Unable to find menu builder "FooBundle:Fake" in bundles BarBundle, FooBundle.
     */
    public function testBundleInheritanceWrongClass()
    {
        $provider = new BuilderAliasProvider(
            $this->createTestKernel(),
            $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface'),
            $this->getMock('Knp\Menu\FactoryInterface')
        );

        $provider->get('FooBundle:Fake:mainMenu');
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
        $bundle->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('FooBundle'))
        ;

        $kernel = $this->getMock('Symfony\Component\HttpKernel\KernelInterface');
        $kernel->expects($this->once())
            ->method('getBundle')
            ->with('FooBundle', false)
            ->will($this->returnValue(array($bundle)))
        ;

        return $kernel;
    }

    private function createTestKernel($childNamespace = 'Bar', $parentNamespace = 'Knp\Bundle\MenuBundle\Tests\Stubs')
    {
        $bundleInterface = version_compare(Kernel::VERSION, '2.1-dev', '<')
            ? 'Knp\Bundle\MenuBundle\Tests\Stubs\ContainerAwareBundleInterface' // Symfony 2.0 misses the extend in the interface
            : 'Symfony\Component\HttpKernel\Bundle\BundleInterface';

        $bundle = $this->getMock($bundleInterface);
        $bundle->expects($this->any())
            ->method('getNamespace')
            ->will($this->returnValue($parentNamespace))
        ;
        $bundle->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('FooBundle'))
        ;

        $childBundle = $this->getMock($bundleInterface);
        $childBundle->expects($this->any())
            ->method('getNamespace')
            ->will($this->returnValue($childNamespace))
        ;
        $childBundle->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('BarBundle'))
        ;
        $childBundle->expects($this->any())
            ->method('getParent')
            ->will($this->returnValue('FooBundle'))
        ;

        $kernel = new TestKernel(array($bundle, $childBundle));
        $kernel->boot();

        return $kernel;
    }
}
