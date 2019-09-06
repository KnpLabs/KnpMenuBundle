<?php

namespace Knp\Bundle\MenuBundle\Tests\DependencyInjection\Compiler;

use Knp\Bundle\MenuBundle\DependencyInjection\Compiler\AddVotersPass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Argument\IteratorArgument;
use Symfony\Component\DependencyInjection\Reference;

class AddVotersPassTest extends TestCase
{
    public function testProcessWithoutProviderDefinition()
    {
        $containerBuilder = $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerBuilder')->getMock();
        $containerBuilder->expects($this->once())
            ->method('hasDefinition')
            ->willReturn(false);
        $containerBuilder->expects($this->never())
            ->method('findTaggedServiceIds');

        $menuPass = new AddVotersPass();

        $menuPass->process($containerBuilder);
    }

    public function testProcessWithAlias()
    {
        $definitionMock = $this->getMockBuilder('Symfony\Component\DependencyInjection\Definition')
            ->disableOriginalConstructor()
            ->getMock();

        $voters = [new Reference('id'), new Reference('foo'), new Reference('bar')];

        if (class_exists(IteratorArgument::class)) {
            $voters = new IteratorArgument($voters);
        }

        $definitionMock->expects($this->once())
            ->method('replaceArgument')
            ->with(0, $voters);

        $listenerMock = $this->getMockBuilder('Symfony\Component\DependencyInjection\Definition')
            ->disableOriginalConstructor()
            ->getMock();

        $containerBuilderMock = $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerBuilder')->getMock();
        $containerBuilderMock->expects($this->once())
            ->method('hasDefinition')
            ->willReturn(true);
        $containerBuilderMock->expects($this->once())
            ->method('findTaggedServiceIds')
            ->with($this->equalTo('knp_menu.voter'))
            ->willReturn(['id' => [[]], 'bar' => [['priority' => -5, 'request' => false]], 'foo' => [[]]]);
        $containerBuilderMock->expects($this->at(1))
            ->method('getDefinition')
            ->with($this->equalTo('knp_menu.matcher'))
            ->willReturn($definitionMock);
        $containerBuilderMock->expects($this->at(2))
            ->method('getDefinition')
            ->with($this->equalTo('knp_menu.listener.voters'))
            ->willReturn($listenerMock);
        $containerBuilderMock->expects($this->once())
            ->method('removeDefinition')
            ->with('knp_menu.listener.voters');

        $menuPass = new AddVotersPass();
        $menuPass->process($containerBuilderMock);
    }

    /**
     * @group legacy
     */
    public function testProcessRequestAware()
    {
        $definitionMock = $this->getMockBuilder('Symfony\Component\DependencyInjection\Definition')
            ->disableOriginalConstructor()
            ->getMock();

        $voters = [new Reference('id'), new Reference('foo'), new Reference('bar')];

        if (class_exists(IteratorArgument::class)) {
            $voters = new IteratorArgument($voters);
        }

        $definitionMock->expects($this->once())
            ->method('replaceArgument')
            ->with(0, $voters);

        $listenerMock = $this->getMockBuilder('Symfony\Component\DependencyInjection\Definition')
            ->disableOriginalConstructor()
            ->getMock();

        $containerBuilderMock = $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerBuilder')->getMock();
        $containerBuilderMock->expects($this->once())
            ->method('hasDefinition')
            ->willReturn(true);
        $containerBuilderMock->expects($this->once())
            ->method('findTaggedServiceIds')
            ->with($this->equalTo('knp_menu.voter'))
            ->willReturn(['id' => [[]], 'bar' => [['priority' => -5, 'request' => false]], 'foo' => [['request' => false]]]);
        $containerBuilderMock->expects($this->at(1))
            ->method('getDefinition')
            ->with($this->equalTo('knp_menu.matcher'))
            ->willReturn($definitionMock);
        $containerBuilderMock->expects($this->at(2))
            ->method('getDefinition')
            ->with($this->equalTo('knp_menu.listener.voters'))
            ->willReturn($listenerMock);

        $menuPass = new AddVotersPass();
        $menuPass->process($containerBuilderMock);
    }
}
