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
        $definitionMock->expects($this->once())
            ->method('addArgument')
            ->with(
                $this->equalTo(
                    [
                        new Reference('id'),
                        new Reference('foo'),
                        new Reference('bar'),
                    ]
                )
            );

        $voterDefinitionMock = $this->getMockBuilder('Symfony\Component\DependencyInjection\Definition')
            ->disableOriginalConstructor()
            ->getMock();
        $voterDefinitionMock->expects($this->once())
            ->method('addArgument')
            ->with($this->equalTo(new Reference('request_stack')));

        $containerBuilderMock = $this->getMockBuilder(
            'Symfony\Component\DependencyInjection\ContainerBuilder'
        )->getMock();
        $containerBuilderMock->expects($this->once())
            ->method('hasDefinition')
            ->will($this->returnValue(true));
        $containerBuilderMock->expects($this->once())
            ->method('findTaggedServiceIds')
            ->with($this->equalTo('knp_menu.voter'))
            ->will(
                $this->returnValue(
                    array(
                        'id' => array(array()),
                        'bar' => array(array('priority' => -5, 'request' => false)),
                        'foo' => array(array('request' => true)),
                    )
                )
            );;
        $containerBuilderMock->expects($this->at(2))
            ->method('getDefinition')
            ->with($this->equalTo('foo'))
            ->will($this->returnValue($voterDefinitionMock));
        $containerBuilderMock->expects($this->at(3))
            ->method('getDefinition')
            ->with($this->equalTo('knp_menu.matcher'))
            ->will($this->returnValue($definitionMock));

        $menuPass = new AddVotersPass();
        $menuPass->process($containerBuilderMock);
    }
}
