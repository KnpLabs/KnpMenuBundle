<?php

namespace Knp\Bundle\MenuBundle\Tests\DependencyInjection\Compiler;

use Knp\Bundle\MenuBundle\DependencyInjection\Compiler\AddExtensionsPass;
use Knp\Menu\FactoryInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Reference;

class AddExtensionsPassTest extends TestCase
{
    public function testProcessWithoutProviderDefinition()
    {
        $containerBuilder = $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerBuilder')->getMock();
        $containerBuilder->expects($this->once())
            ->method('has')
            ->willReturn(false);
        $containerBuilder->expects($this->never())
            ->method('findTaggedServiceIds');

        $menuPass = new AddExtensionsPass();

        $menuPass->process($containerBuilder);
    }

    public function testProcessWithAlias()
    {
        $menuFactoryClass = 'Knp\Bundle\MenuBundle\Tests\DependencyInjection\Compiler\MenuFactoryMock';

        $definitionMock = $this->getMockBuilder('Symfony\Component\DependencyInjection\Definition')
            ->disableOriginalConstructor()
            ->getMock();
        $definitionMock->expects($this->at(0))
            ->method('getClass')
            ->willReturn($menuFactoryClass);
        $definitionMock->expects($this->at(1))
            ->method('addMethodCall')
            ->with($this->equalTo('addExtension'), $this->equalTo([new Reference('id'), 0]));
        $definitionMock->expects($this->at(2))
            ->method('addMethodCall')
            ->with($this->equalTo('addExtension'), $this->equalTo([new Reference('id'), 12]));
        $definitionMock->expects($this->at(3))
            ->method('addMethodCall')
            ->with($this->equalTo('addExtension'), $this->equalTo([new Reference('foo'), -4]));

        $parameterBagMock = $this->getMockBuilder('Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface')->getMock();
        $parameterBagMock->expects($this->once())
            ->method('resolveValue')
            ->with($menuFactoryClass)
            ->willReturn($menuFactoryClass);

        $containerBuilderMock = $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerBuilder')->getMock();
        $containerBuilderMock->expects($this->once())
            ->method('has')
            ->willReturn(true);
        $containerBuilderMock->expects($this->once())
            ->method('findTaggedServiceIds')
            ->with($this->equalTo('knp_menu.factory_extension'))
            ->willReturn(['id' => ['tag1' => [], 'tag2' => ['priority' => 12]], 'foo' => ['tag1' => ['priority' => -4]]]);
        $containerBuilderMock->expects($this->once())
            ->method('findDefinition')
            ->with($this->equalTo('knp_menu.factory'))
            ->willReturn($definitionMock);
        $containerBuilderMock->expects($this->once())
            ->method('getParameterBag')
            ->willReturn($parameterBagMock);

        $menuPass = new AddExtensionsPass();
        $menuPass->process($containerBuilderMock);
    }

    public function testMissingAddExtension()
    {
        $definitionMock = $this->getMockBuilder('Symfony\Component\DependencyInjection\Definition')
            ->disableOriginalConstructor()
            ->getMock();
        $definitionMock->expects($this->at(0))
            ->method('getClass')
            ->willReturn('SimpleMenuFactory');

        $parameterBagMock = $this->getMockBuilder('Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface')->getMock();
        $parameterBagMock->expects($this->once())
            ->method('resolveValue')
            ->with('SimpleMenuFactory')
            ->willReturn('SimpleMenuFactory');

        $containerBuilderMock = $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerBuilder')->getMock();
        $containerBuilderMock->expects($this->once())
            ->method('has')
            ->willReturn(true);
        $containerBuilderMock->expects($this->once())
            ->method('findTaggedServiceIds')
            ->with($this->equalTo('knp_menu.factory_extension'))
            ->willReturn(['id' => ['tag1' => [], 'tag2' => ['priority' => 12]], 'foo' => ['tag1' => ['priority' => -4]]]);
        $containerBuilderMock->expects($this->once())
            ->method('findDefinition')
            ->with($this->equalTo('knp_menu.factory'))
            ->willReturn($definitionMock);
        $containerBuilderMock->expects($this->once())
            ->method('getParameterBag')
            ->willReturn($parameterBagMock);

        $this->expectException(\Symfony\Component\Config\Definition\Exception\InvalidConfigurationException::class);

        $menuPass = new AddExtensionsPass();
        $menuPass->process($containerBuilderMock);
    }
}

class MenuFactoryMock implements FactoryInterface
{
    public function createItem($name, array $options = [])
    {
    }

    public function addExtension()
    {
    }
}
