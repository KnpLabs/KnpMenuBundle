<?php
namespace Knp\Bundle\MenuBundle\Tests\DependencyInjection\Compiler;

use Knp\Bundle\MenuBundle\DependencyInjection\Compiler\AddExtensionsPass;
use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\Reference;

class AddExtensionsPassTest extends \PHPUnit_Framework_TestCase
{
    public function testProcessWithoutProviderDefinition()
    {
        $containerBuilder = $this->getMock('Symfony\Component\DependencyInjection\ContainerBuilder');
        $containerBuilder->expects($this->once())
            ->method('has')
            ->will($this->returnValue(false));
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
            ->will($this->returnValue($menuFactoryClass));
        $definitionMock->expects($this->at(1))
            ->method('addMethodCall')
            ->with($this->equalTo('addExtension'), $this->equalTo(array(new Reference('id'), 0)));
        $definitionMock->expects($this->at(2))
            ->method('addMethodCall')
            ->with($this->equalTo('addExtension'), $this->equalTo(array(new Reference('id'), 12)));
        $definitionMock->expects($this->at(3))
            ->method('addMethodCall')
            ->with($this->equalTo('addExtension'), $this->equalTo(array(new Reference('foo'), -4)));

        $parameterBagMock = $this->getMock('Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface');
        $parameterBagMock->expects($this->once())
            ->method('resolveValue')
            ->with($menuFactoryClass)
            ->will($this->returnValue($menuFactoryClass));

        $containerBuilderMock = $this->getMock('Symfony\Component\DependencyInjection\ContainerBuilder');
        $containerBuilderMock->expects($this->once())
            ->method('has')
            ->will($this->returnValue(true));
        $containerBuilderMock->expects($this->once())
            ->method('findTaggedServiceIds')
            ->with($this->equalTo('knp_menu.factory_extension'))
            ->will($this->returnValue(array('id' => array('tag1' => array(), 'tag2' => array('priority' => 12)), 'foo' => array('tag1' => array('priority' => -4)))));
        $containerBuilderMock->expects($this->once())
            ->method('findDefinition')
            ->with($this->equalTo('knp_menu.factory'))
            ->will($this->returnValue($definitionMock));
        $containerBuilderMock->expects($this->once())
            ->method('getParameterBag')
            ->will($this->returnValue($parameterBagMock));

        $menuPass = new AddExtensionsPass();
        $menuPass->process($containerBuilderMock);
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testMissingAddExtension()
    {
        $definitionMock = $this->getMockBuilder('Symfony\Component\DependencyInjection\Definition')
            ->disableOriginalConstructor()
            ->getMock();
        $definitionMock->expects($this->at(0))
            ->method('getClass')
            ->will($this->returnValue('SimpleMenuFactory'));

        $parameterBagMock = $this->getMock('Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface');
        $parameterBagMock->expects($this->once())
            ->method('resolveValue')
            ->with('SimpleMenuFactory')
            ->will($this->returnValue('SimpleMenuFactory'));

        $containerBuilderMock = $this->getMock('Symfony\Component\DependencyInjection\ContainerBuilder');
        $containerBuilderMock->expects($this->once())
            ->method('has')
            ->will($this->returnValue(true));
        $containerBuilderMock->expects($this->once())
            ->method('findTaggedServiceIds')
            ->with($this->equalTo('knp_menu.factory_extension'))
            ->will($this->returnValue(array('id' => array('tag1' => array(), 'tag2' => array('priority' => 12)), 'foo' => array('tag1' => array('priority' => -4)))));
        $containerBuilderMock->expects($this->once())
            ->method('findDefinition')
            ->with($this->equalTo('knp_menu.factory'))
            ->will($this->returnValue($definitionMock));
        $containerBuilderMock->expects($this->once())
            ->method('getParameterBag')
            ->will($this->returnValue($parameterBagMock));

        $menuPass = new AddExtensionsPass();
        $menuPass->process($containerBuilderMock);
    }
}

class MenuFactoryMock implements FactoryInterface
{
    public function createItem($name, array $options = array())
    {
    }

    public function addExtension()
    {
    }
}
