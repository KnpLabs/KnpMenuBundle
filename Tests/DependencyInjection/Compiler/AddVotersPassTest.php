<?php

namespace Knp\Bundle\MenuBundle\Tests\DependencyInjection\Compiler;

use Knp\Bundle\MenuBundle\DependencyInjection\Compiler\AddVotersPass;
use Symfony\Component\DependencyInjection\Reference;

class AddVotersPassTest extends \PHPUnit_Framework_TestCase
{
    public function testProcessWithoutProviderDefinition()
    {
        $menuPass = new AddVotersPass();

        $this->assertNull($menuPass->process($this->getMock('Symfony\Component\DependencyInjection\ContainerBuilder')));
    }

    public function testProcessWithAlias()
    {
        $definitionMock = $this->getMockBuilder('Symfony\Component\DependencyInjection\Definition')
            ->disableOriginalConstructor()
            ->getMock();
        $definitionMock->expects($this->once())
            ->method('addMethodCall')
            ->with($this->equalTo('addVoter'), $this->equalTo(array(new Reference('id'))));

        $containerBuilderMock = $this->getMock('Symfony\Component\DependencyInjection\ContainerBuilder');
        $containerBuilderMock->expects($this->once())
            ->method('hasDefinition')
            ->will($this->returnValue(true));
        $containerBuilderMock->expects($this->once())
            ->method('findTaggedServiceIds')
            ->with($this->equalTo('knp_menu.voter'))
            ->will($this->returnValue(array('id' => array('tag1' => array()))));
        $containerBuilderMock->expects($this->once())
            ->method('getDefinition')
            ->with($this->equalTo('knp_menu.matcher'))
            ->will($this->returnValue($definitionMock));

        $menuPass = new AddVotersPass();
        $menuPass->process($containerBuilderMock);
    }
}
