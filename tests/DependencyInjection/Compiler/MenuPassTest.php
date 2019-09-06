<?php

namespace Knp\Bundle\MenuBundle\Tests\DependencyInjection\Compiler;

use Knp\Bundle\MenuBundle\DependencyInjection\Compiler\MenuPass;
use PHPUnit\Framework\TestCase;

class MenuPassTest extends TestCase
{
    public function testProcessWithoutProviderDefinition()
    {
        $containerBuilder = $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerBuilder')->getMock();
        $containerBuilder->expects($this->once())
            ->method('hasDefinition')
            ->willReturn(false);
        $containerBuilder->expects($this->never())
            ->method('findTaggedServiceIds');

        $menuPass = new MenuPass();

        $menuPass->process($containerBuilder);
    }

    public function testProcessWithEmptyAlias()
    {
        $this->expectException(\InvalidArgumentException::class);

        $containerBuilderMock = $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerBuilder')->getMock();
        $containerBuilderMock->expects($this->once())
            ->method('hasDefinition')
            ->willReturn(true);
        $containerBuilderMock->expects($this->once())
            ->method('findTaggedServiceIds')
            ->with($this->equalTo('knp_menu.menu'))
            ->willReturn(['id' => ['tag1' => ['alias' => '']]]);

        $menuPass = new MenuPass();
        $menuPass->process($containerBuilderMock);
    }

    public function testProcessWithAlias()
    {
        $definitionMock = $this->getMockBuilder('Symfony\Component\DependencyInjection\Definition')
            ->disableOriginalConstructor()
            ->getMock();
        $definitionMock->expects($this->once())
            ->method('replaceArgument')
            ->with($this->equalTo(1), $this->equalTo(['test_alias' => 'id']));

        $containerBuilderMock = $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerBuilder')->getMock();
        $containerBuilderMock->expects($this->once())
            ->method('hasDefinition')
            ->willReturn(true);
        $containerBuilderMock->expects($this->once())
            ->method('findTaggedServiceIds')
            ->with($this->equalTo('knp_menu.menu'))
            ->willReturn(['id' => ['tag1' => ['alias' => 'test_alias']]]);
        $containerBuilderMock->expects($this->once())
            ->method('getDefinition')
            ->with($this->equalTo('knp_menu.menu_provider.container_aware'))
            ->willReturn($definitionMock);

        $menuPass = new MenuPass();
        $menuPass->process($containerBuilderMock);
    }
}
