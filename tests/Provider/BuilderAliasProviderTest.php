<?php

namespace Knp\Bundle\MenuBundle\Tests\Provider;

use Knp\Bundle\MenuBundle\Provider\BuilderAliasProvider;
use Knp\Bundle\MenuBundle\Tests\Stubs\TestKernel;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

class BuilderAliasProviderTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        if (!interface_exists(ContainerAwareInterface::class)) {
            self::markTestSkipped();
        }
    }

    public function testHas(): void
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

    public function testGetExistentMenu(): void
    {
        $item = $this->getMockBuilder('Knp\Menu\ItemInterface')->getMock();
        // mock the factory to return a set value when the builder creates the menu
        $factory = $this->getMockBuilder('Knp\Menu\FactoryInterface')->getMock();
        $factory->expects($this->once())
            ->method('createItem')
            ->with('Main menu')
            ->willReturn($item);

        $provider = new BuilderAliasProvider(
            $this->createMockKernelForStub(),
            $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerInterface')->getMock(),
            $factory
        );

        $menu = $provider->get('FooBundle:Builder:mainMenu');
        // returns the mocked value returned from mocked factory
        $this->assertSame($item, $menu);
    }

    public function testGetContainerAwareMenu(): void
    {
        $item = $this->getMockBuilder('Knp\Menu\ItemInterface')->getMock();
        // mock the factory to return a set value when the builder creates the menu
        $factory = $this->getMockBuilder('Knp\Menu\FactoryInterface')->getMock();
        $factory->expects($this->once())
            ->method('createItem')
            ->with('Main menu')
            ->willReturn($item);

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

    public function testGetInvalidReturnValue(): void
    {
        $provider = new BuilderAliasProvider(
            $this->createMockKernelForStub(),
            $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerInterface')->getMock(),
            $this->getMockBuilder('Knp\Menu\FactoryInterface')->getMock()
        );

        $this->expectException(\InvalidArgumentException::class);
        $provider->get('FooBundle:Builder:invalidMethod');
    }

    public function testGetNonExistentMenu(): void
    {
        $provider = new BuilderAliasProvider(
            $this->getMockBuilder('Symfony\Component\HttpKernel\KernelInterface')->getMock(),
            $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerInterface')->getMock(),
            $this->getMockBuilder('Knp\Menu\FactoryInterface')->getMock()
        );
        $this->expectException(\InvalidArgumentException::class);
        $provider->get('non-existent');
    }

    public function testGetNonExistentMenuClass(): void
    {
        $provider = new BuilderAliasProvider(
            $this->createMockKernelForStub(),
            $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerInterface')->getMock(),
            $this->getMockBuilder('Knp\Menu\FactoryInterface')->getMock()
        );

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Unable to find menu builder "Knp\\Bundle\\MenuBundle\\Tests\\Stubs\\Menu\\Fake" in bundle FooBundle:Fake.');
        $provider->get('FooBundle:Fake:mainMenu');
    }

    public function testGetNonExistentMenuMethod(): void
    {
        $provider = new BuilderAliasProvider(
            $this->createMockKernelForStub(),
            $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerInterface')->getMock(),
            $this->getMockBuilder('Knp\Menu\FactoryInterface')->getMock()
        );

        // bundle will return a null namespace, class won't be found
        $this->expectException(\InvalidArgumentException::class);
        $provider->get('FooBundle:Builder:fakeMenu');
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
            ->willReturn('Knp\Bundle\MenuBundle\Tests\Stubs')
        ;
        $bundle->expects($this->any())
            ->method('getName')
            ->willReturn('FooBundle')
        ;

        $kernel = $this->getMockBuilder('Symfony\Component\HttpKernel\KernelInterface')->getMock();
        $kernel->expects($this->once())
            ->method('getBundle')
            ->with('FooBundle')
            ->willReturn($bundle)
        ;

        return $kernel;
    }

    private function createTestKernel($childNamespace = 'Bar', $parentNamespace = 'Knp\Bundle\MenuBundle\Tests\Stubs'): TestKernel
    {
        $bundle = $this->getMockBuilder('Symfony\Component\HttpKernel\Bundle\BundleInterface')->getMock();
        $bundle->expects($this->any())
            ->method('getNamespace')
            ->willReturn($parentNamespace)
        ;
        $bundle->expects($this->any())
            ->method('getName')
            ->willReturn('FooBundle')
        ;

        $childBundle = $this->getMockBuilder('Symfony\Component\HttpKernel\Bundle\BundleInterface')->getMock();
        $childBundle->expects($this->any())
            ->method('getNamespace')
            ->willReturn($childNamespace)
        ;
        $childBundle->expects($this->any())
            ->method('getName')
            ->willReturn('BarBundle')
        ;
        $childBundle->expects($this->any())
            ->method('getParent')
            ->willReturn('FooBundle')
        ;

        $kernel = new TestKernel([$bundle, $childBundle]);
        $kernel->boot();

        return $kernel;
    }
}
