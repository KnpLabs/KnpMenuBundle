<?php

namespace Knp\Bundle\MenuBundle\Tests\DependencyInjection\Compiler;

use Knp\Bundle\MenuBundle\DependencyInjection\Compiler\AddVotersPass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Reference;

class AddVotersPassTest extends TestCase
{
    public function testProcessWithoutProviderDefinition()
    {
        $containerBuilder = $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerBuilder')->getMock();
        $containerBuilder->expects($this->once())
            ->method('hasDefinition')
            ->will($this->returnValue(false));
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
        $definitionMock->expects($this->at(0))
            ->method('addMethodCall')
            ->with($this->equalTo('addVoter'), $this->equalTo(array(new Reference('id'))));
        $definitionMock->expects($this->at(1))
            ->method('addMethodCall')
            ->with($this->equalTo('addVoter'), $this->equalTo(array(new Reference('foo'))));
        $definitionMock->expects($this->at(2))
            ->method('addMethodCall')
            ->with($this->equalTo('addVoter'), $this->equalTo(array(new Reference('bar'))));

        $listenerMock = $this->getMockBuilder('Symfony\Component\DependencyInjection\Definition')
            ->disableOriginalConstructor()
            ->getMock();
        $listenerMock->expects($this->once())
            ->method('addMethodCall')
            ->with($this->equalTo('addVoter'), $this->equalTo(array(new Reference('foo'))));

        $containerBuilderMock = $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerBuilder')->getMock();
        $containerBuilderMock->expects($this->once())
            ->method('hasDefinition')
            ->will($this->returnValue(true));
        $containerBuilderMock->expects($this->once())
            ->method('findTaggedServiceIds')
            ->with($this->equalTo('knp_menu.voter'))
            ->will($this->returnValue(array('id' => array(array()), 'bar' => array(array('priority' => -5, 'request' => false)), 'foo' => array(array('request' => true)))));
        $containerBuilderMock->expects($this->at(1))
            ->method('getDefinition')
            ->with($this->equalTo('knp_menu.matcher'))
            ->will($this->returnValue($definitionMock));
        $containerBuilderMock->expects($this->at(2))
            ->method('getDefinition')
            ->with($this->equalTo('knp_menu.listener.voters'))
            ->will($this->returnValue($listenerMock));

        $menuPass = new AddVotersPass();
        $menuPass->process($containerBuilderMock);
    }
}
