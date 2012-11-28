<?php

namespace Knp\Bundle\MenuBundle\Tests\DependencyInjection\Compiler;

use Knp\Bundle\MenuBundle\DependencyInjection\Compiler\AddTemplatePathPass;

class AddTemplatePathPassTest extends \PHPUnit_Framework_TestCase
{
    public function testProcessWithoutProviderDefinition()
    {
        $templatePathPass = new AddTemplatePathPass();

        $this->assertNull($templatePathPass->process($this->getMock('Symfony\Component\DependencyInjection\ContainerBuilder')));
    }

    public function testProcess()
    {
        $definitionMock = $this->getMockBuilder('Symfony\Component\DependencyInjection\Definition')
            ->disableOriginalConstructor()
            ->getMock();
        $definitionMock->expects($this->once())
            ->method('addMethodCall')
            ->with($this->equalTo('addPath'), $this->isType('array'));

        $containerBuilderMock = $this->getMock('Symfony\Component\DependencyInjection\ContainerBuilder');
        $containerBuilderMock->expects($this->once())
            ->method('hasDefinition')
            ->will($this->returnValue(true));
        $containerBuilderMock->expects($this->once())
            ->method('getDefinition')
            ->with($this->equalTo('twig.loader.filesystem'))
            ->will($this->returnValue($definitionMock));

        $templatePathPass = new AddTemplatePathPass();
        $templatePathPass->process($containerBuilderMock);
    }

    public function testProcessLegacy()
    {
        $definitionMock = $this->getMockBuilder('Symfony\Component\DependencyInjection\Definition')
            ->disableOriginalConstructor()
            ->getMock();
        $definitionMock->expects($this->once())
            ->method('addMethodCall')
            ->with($this->equalTo('addPath'), $this->isType('array'));

        $containerBuilderMock = $this->getMock('Symfony\Component\DependencyInjection\ContainerBuilder');
        $containerBuilderMock->expects($this->exactly(2))
            ->method('hasDefinition')
            ->will($this->onConsecutiveCalls(false, true));
        $containerBuilderMock->expects($this->once())
            ->method('getDefinition')
            ->with($this->equalTo('twig.loader'))
            ->will($this->returnValue($definitionMock));

        $templatePathPass = new AddTemplatePathPass();
        $templatePathPass->process($containerBuilderMock);
    }
}
