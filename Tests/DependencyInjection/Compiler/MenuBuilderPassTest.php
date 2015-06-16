<?php

namespace Knp\Bundle\MenuBundle\Tests\DependencyInjection\Compiler;

use Knp\Bundle\MenuBundle\DependencyInjection\Compiler\MenuBuilderPass;

class MenuBuilderPassTest extends \PHPUnit_Framework_TestCase
{
    private $containerBuilder;
    private $definition;

    /**
     * @var MenuBuilderPass
     */
    private $pass;

    protected function setUp()
    {
        $this->containerBuilder = $this->prophesize('Symfony\Component\DependencyInjection\ContainerBuilder');
        $this->definition = $this->prophesize('Symfony\Component\DependencyInjection\Definition');
        $this->pass = new MenuBuilderPass();

        $this->containerBuilder->getDefinition('knp_menu.menu_provider.builder_service')->willReturn($this->definition);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage The alias is not defined in the "knp_menu.menu_builder" tag for the service "id"
     */
    public function testFailsWhenAliasIsMissing()
    {
        $this->containerBuilder->findTaggedServiceIds('knp_menu.menu_builder')->willReturn(array('id' => array(array('alias' => ''))));

        $this->pass->process($this->containerBuilder->reveal());
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage The method is not defined in the "knp_menu.menu_builder" tag for the service "id"
     */
    public function testFailsWhenMethodIsMissing()
    {
        $this->containerBuilder->findTaggedServiceIds('knp_menu.menu_builder')->willReturn(array('id' => array(array('alias' => 'foo'))));

        $this->pass->process($this->containerBuilder->reveal());
    }

    public function testReplaceArgument()
    {
        $taggedServiceIds = array(
            'id1' => array(array('alias' => 'foo', 'method' => 'fooMenu'), array('alias' => 'bar', 'method' => 'bar')),
            'id2' => array(array('alias' => 'foo', 'method' => 'fooBar'), array('alias' => 'baz', 'method' => 'bar')),
        );
        $this->containerBuilder->findTaggedServiceIds('knp_menu.menu_builder')->willReturn($taggedServiceIds);

        $menuBuilders = array(
            'foo' => array('id2', 'fooBar'),
            'bar' => array('id1', 'bar'),
            'baz' => array('id2', 'bar'),
        );
        $this->definition->replaceArgument(1, $menuBuilders)->shouldBeCalled();

        $this->pass->process($this->containerBuilder->reveal());
    }
}
