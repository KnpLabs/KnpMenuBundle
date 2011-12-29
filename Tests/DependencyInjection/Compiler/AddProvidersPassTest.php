<?php

namespace Knp\Bundle\MenuBundle\Tests\DependencyInjection\Compiler;

use Knp\Bundle\MenuBundle\DependencyInjection\Compiler\AddProvidersPass;

class AddProvidersPassTest extends \PHPUnit_Framework_TestCase
{
    public function testProcessWithoutProviderDefinition()
    {
        $providersPass = new AddProvidersPass();

        $this->assertNull($providersPass->process($this->getMock('Symfony\Component\DependencyInjection\ContainerBuilder')));
    }

    public function testProcessForOneProvider()
    {
        $containerBuilderMock = $this->getMock('Symfony\Component\DependencyInjection\ContainerBuilder');
        $containerBuilderMock->expects($this->once())
            ->method('hasDefinition')
            ->will($this->returnValue(true));
        $containerBuilderMock->expects($this->once())
            ->method('findTaggedServiceIds')
            ->with($this->equalTo('knp_menu.provider'))
            ->will($this->returnValue(array('id' => array('provider_tag1'))));
        $containerBuilderMock->expects($this->once())
            ->method('setAlias')
            ->with(
                $this->equalTo('knp_menu.menu_provider'),
                $this->equalTo('id')
            );

        $providersPass = new AddProvidersPass();
        $providersPass->process($containerBuilderMock);
    }

    public function testProcessForManyProviders()
    {
        $definitionMock = $this->getMockBuilder('Symfony\Component\DependencyInjection\Definition')
            ->disableOriginalConstructor()
            ->getMock();
        $definitionMock->expects($this->once())
            ->method('replaceArgument')
            ->with($this->equalTo(0), $this->isType('array'));

        $containerBuilderMock = $this->getMock('Symfony\Component\DependencyInjection\ContainerBuilder');
        $containerBuilderMock->expects($this->once())
            ->method('hasDefinition')
            ->will($this->returnValue(true));
        $containerBuilderMock->expects($this->once())
            ->method('findTaggedServiceIds')
            ->with($this->equalTo('knp_menu.provider'))
            ->will($this->returnValue(array(
                'id' => array('provider_tag1'),
                'id2' => array('provider_tag2')
            )));
        $containerBuilderMock->expects($this->once())
            ->method('setAlias')
            ->with(
                $this->equalTo('knp_menu.menu_provider'),
                $this->equalTo('knp_menu.menu_provider.chain')
            );
        $containerBuilderMock->expects($this->once())
            ->method('getDefinition')
            ->with($this->equalTo('knp_menu.menu_provider.chain'))
            ->will($this->returnValue($definitionMock));

        $providersPass = new AddProvidersPass();
        $providersPass->process($containerBuilderMock);
    }
}
