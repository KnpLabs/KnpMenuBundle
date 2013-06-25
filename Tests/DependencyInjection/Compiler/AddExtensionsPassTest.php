<?php
namespace Knp\Bundle\MenuBundle\Tests\DependencyInjection\Compiler;

use Knp\Bundle\MenuBundle\DependencyInjection\Compiler\AddExtensionsPass;
use Symfony\Component\DependencyInjection\Reference;

class AddExtensionsPassTest extends \PHPUnit_Framework_TestCase
{
    public function testProcessWithoutProviderDefinition()
    {
        $containerBuilder = $this->getMock('Symfony\Component\DependencyInjection\ContainerBuilder');
        $containerBuilder->expects($this->once())
            ->method('hasDefinition')
            ->will($this->returnValue(false));
        $containerBuilder->expects($this->never())
            ->method('findTaggedServiceIds');

        $menuPass = new AddExtensionsPass();

        $menuPass->process($containerBuilder);
    }

    public function testProcessWithAlias()
    {
        $definitionMock = $this->getMockBuilder('Symfony\Component\DependencyInjection\Definition')
            ->disableOriginalConstructor()
            ->getMock();
        $definitionMock->expects($this->at(0))
            ->method('addMethodCall')
            ->with($this->equalTo('addExtension'), $this->equalTo(array(new Reference('id'), 0)));
        $definitionMock->expects($this->at(1))
            ->method('addMethodCall')
            ->with($this->equalTo('addExtension'), $this->equalTo(array(new Reference('id'), 12)));
        $definitionMock->expects($this->at(2))
            ->method('addMethodCall')
            ->with($this->equalTo('addExtension'), $this->equalTo(array(new Reference('foo'), -4)));

        $containerBuilderMock = $this->getMock('Symfony\Component\DependencyInjection\ContainerBuilder');
        $containerBuilderMock->expects($this->once())
            ->method('hasDefinition')
            ->will($this->returnValue(true));
        $containerBuilderMock->expects($this->once())
            ->method('findTaggedServiceIds')
            ->with($this->equalTo('knp_menu.factory_extension'))
            ->will($this->returnValue(array('id' => array('tag1' => array(), 'tag2' => array('priority' => 12)), 'foo' => array('tag1' => array('priority' => -4)))));
        $containerBuilderMock->expects($this->once())
            ->method('getDefinition')
            ->with($this->equalTo('knp_menu.factory'))
            ->will($this->returnValue($definitionMock));

        $menuPass = new AddExtensionsPass();
        $menuPass->process($containerBuilderMock);
    }
}
