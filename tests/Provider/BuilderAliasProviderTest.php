<?php

namespace Knp\Bundle\MenuBundle\Tests\Provider;

use Knp\Bundle\MenuBundle\Provider\BuilderAliasProvider;
use Knp\Bundle\MenuBundle\Tests\Stubs\TestKernel;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

class BuilderAliasProviderTest extends TestCase
{
    public function testHas()
    {
        $provider = new BuilderAliasProvider(
            $this->getMockBuilder('Symfony\Component\HttpKernel\KernelInterface')->getMock(),
            $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerInterface')->getMock(),
            $this->getMockBuilder('Knp\Menu\FactoryInterface')->getMock()
        );
        $this->assertFalse($provider->has('foo'));
        $this->assertFalse($provider->has('foo:bar'));
        $this->assertTrue($provider->has('foo:bar:baz'));
    }

    public function testGetExistentMenu()
    {
        $item = $this->getMockBuilder('Knp\Menu\ItemInterface')->getMock();
        // mock the factory to return a set value when the builder creates the menu
        $factory = $this->getMockBuilder('Knp\Menu\FactoryInterface')->getMock();
        $factory->expects($this->once())
            ->method('createItem')
            ->with('Main menu')
            ->will($this->returnValue($item));

        $provider = new BuilderAliasProvider(
            $this->createMockKernelForStub(),
            $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerInterface')->getMock(),
            $factory
        );

        $menu = $provider->get('FooBundle:Builder:mainMenu');
        // returns the mocked value returned from mocked factory
        $this->assertSame($item, $menu);
    }

    public function testGetContainerAwareMenu()
    {
        $item = $this->getMockBuilder('Knp\Menu\ItemInterface')->getMock();
        // mock the factory to return a set value when the builder creates the menu
        $factory = $this->getMockBuilder('Knp\Menu\FactoryInterface')->getMock();
        $factory->expects($this->once())
            ->method('createItem')
            ->with('Main menu')
            ->will($this->returnValue($item));

        $container = $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerInterface')->getMock();
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
     * @expectedException \InvalidArgumentException
     */
    public function testGetInvalidReturnValue()
    {
        $provider = new BuilderAliasProvider(
            $this->createMockKernelForStub(),
            $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerInterface')->getMock(),
            $this->getMockBuilder('Knp\Menu\FactoryInterface')->getMock()
        );

        $provider->get('FooBundle:Builder:invalidMethod');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetNonExistentMenu()
    {
        $provider = new BuilderAliasProvider(
            $this->getMockBuilder('Symfony\Component\HttpKernel\KernelInterface')->getMock(),
            $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerInterface')->getMock(),
            $this->getMockBuilder('Knp\Menu\FactoryInterface')->getMock()
        );
        $provider->get('non-existent');
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Class "Knp\Bundle\MenuBundle\Tests\Stubs\Menu\Fake" does not exist for menu builder "FooBundle:Fake".
     */
    public function testGetNonExistentMenuClass()
    {
        $provider = new BuilderAliasProvider(
            $this->createMockKernelForStub(),
            $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerInterface')->getMock(),
            $this->getMockBuilder('Knp\Menu\FactoryInterface')->getMock()
        );

        $provider->get('FooBundle:Fake:mainMenu');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetNonExistentMenuMethod()
    {
        $provider = new BuilderAliasProvider(
            $this->createMockKernelForStub(),
            $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerInterface')->getMock(),
            $this->getMockBuilder('Knp\Menu\FactoryInterface')->getMock()
        );

        // bundle will return a null namespace, class won't be found
        $provider->get('FooBundle:Builder:fakeMenu');
    }

    /**
     * @group legacy
     */
    public function testBundleInheritanceParent()
    {
        if (!method_exists(BundleInterface::class, 'getParent')) {
            $this->markTestSkipped('Bundle inheritance does not exist in this Symfony version.');
        }

        $item = $this->getMockBuilder('Knp\Menu\ItemInterface')->getMock();
        // mock the factory to return a set value when the builder creates the menu
        $factory = $this->getMockBuilder('Knp\Menu\FactoryInterface')->getMock();
        $factory->expects($this->once())
            ->method('createItem')
            ->with('Main menu')
            ->will($this->returnValue($item));

        $provider = new BuilderAliasProvider(
            $this->createTestKernel(),
            $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerInterface')->getMock(),
            $factory
        );

        $menu = $provider->get('FooBundle:Builder:mainMenu');
        // returns the mocked value returned from mocked factory
        $this->assertSame($item, $menu);
    }

    /**
     * @group legacy
     */
    public function testBundleInheritanceChild()
    {
        if (!method_exists(BundleInterface::class, 'getParent')) {
            $this->markTestSkipped('Bundle inheritance does not exist in this Symfony version.');
        }

        $item = $this->getMockBuilder('Knp\Menu\ItemInterface')->getMock();
        // mock the factory to return a set value when the builder creates the menu
        $factory = $this->getMockBuilder('Knp\Menu\FactoryInterface')->getMock();
        $factory->expects($this->once())
            ->method('createItem')
            ->with('Main menu for the child')
            ->will($this->returnValue($item));

        $provider = new BuilderAliasProvider(
            $this->createTestKernel('Knp\Bundle\MenuBundle\Tests\Stubs\Child'),
            $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerInterface')->getMock(),
            $factory
        );

        $menu = $provider->get('FooBundle:Builder:mainMenu');
        // returns the mocked value returned from mocked factory
        $this->assertSame($item, $menu);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Unable to find menu builder "FooBundle:Fake" in bundles BarBundle, FooBundle.
     * @group legacy
     */
    public function testBundleInheritanceWrongClass()
    {
        if (!method_exists(BundleInterface::class, 'getParent')) {
            $this->markTestSkipped('Bundle inheritance does not exist in this Symfony version.');
        }

        $provider = new BuilderAliasProvider(
            $this->createTestKernel(),
            $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerInterface')->getMock(),
            $this->getMockBuilder('Knp\Menu\FactoryInterface')->getMock()
        );

        $provider->get('FooBundle:Fake:mainMenu');
    }

    /**
     * Returns a mocked kernel with a mocked "FooBundle" whose namespace
     * points to the Stubs directory.
     */
    private function createMockKernelForStub()
    {
        $bundle = $this->getMockBuilder('Symfony\Component\HttpKernel\Bundle\BundleInterface')->getMock();
        $bundle->expects($this->once())
            ->method('getNamespace')
            ->will($this->returnValue('Knp\Bundle\MenuBundle\Tests\Stubs'))
        ;
        $bundle->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('FooBundle'))
        ;

        $kernel = $this->getMockBuilder('Symfony\Component\HttpKernel\KernelInterface')->getMock();
        $kernel->expects($this->once())
            ->method('getBundle')
            ->with('FooBundle', false)
            ->will($this->returnValue([$bundle]))
        ;

        return $kernel;
    }

    private function createTestKernel($childNamespace = 'Bar', $parentNamespace = 'Knp\Bundle\MenuBundle\Tests\Stubs')
    {
        $bundle = $this->getMockBuilder('Symfony\Component\HttpKernel\Bundle\BundleInterface')->getMock();
        $bundle->expects($this->any())
            ->method('getNamespace')
            ->will($this->returnValue($parentNamespace))
        ;
        $bundle->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('FooBundle'))
        ;

        $childBundle = $this->getMockBuilder('Symfony\Component\HttpKernel\Bundle\BundleInterface')->getMock();
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

        $kernel = new TestKernel([$bundle, $childBundle]);
        $kernel->boot();

        return $kernel;
    }
}
