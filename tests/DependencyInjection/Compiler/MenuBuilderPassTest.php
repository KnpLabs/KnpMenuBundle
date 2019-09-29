<?php

namespace Knp\Bundle\MenuBundle\Tests\DependencyInjection\Compiler;

use Knp\Bundle\MenuBundle\DependencyInjection\Compiler\MenuBuilderPass;
use PHPUnit\Framework\TestCase;

class MenuBuilderPassTest extends TestCase
{
    private $containerBuilder;
    private $definition;
    private $builderDefinition;

    /**
     * @var MenuBuilderPass
     */
    private $pass;

    protected function setUp(): void
    {
        $this->containerBuilder = $this->prophesize('Symfony\Component\DependencyInjection\ContainerBuilder');
        $this->definition = $this->prophesize('Symfony\Component\DependencyInjection\Definition');
        $this->builderDefinition = $this->prophesize('Symfony\Component\DependencyInjection\Definition');
        $this->pass = new MenuBuilderPass();

        $this->containerBuilder->hasDefinition('knp_menu.menu_provider.builder_service')->willReturn(true);
        $this->containerBuilder->getDefinition('knp_menu.menu_provider.builder_service')->willReturn($this->definition);
        $this->containerBuilder->getDefinition('id')->willReturn($this->builderDefinition);

        $this->builderDefinition->isPublic()->willReturn(true);
        $this->builderDefinition->isAbstract()->willReturn(false);
    }

    public function testNoopWithoutProvider()
    {
        $this->containerBuilder->hasDefinition('knp_menu.menu_provider.builder_service')->willReturn(false);

        $this->containerBuilder->findTaggedServiceIds('knp_menu.menu_builder')->shouldNotBeCalled();

        $this->pass->process($this->containerBuilder->reveal());
    }

    public function testFailsWhenServiceIsAbstract()
    {
        $this->builderDefinition->isAbstract()->willReturn(true);
        $this->containerBuilder->findTaggedServiceIds('knp_menu.menu_builder')->willReturn(['id' => [['alias' => 'foo']]]);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Abstract services cannot be registered as menu builders but "id" is.');
        $this->pass->process($this->containerBuilder->reveal());
    }

    public function testFailsWhenServiceIsPrivate()
    {
        $this->builderDefinition->isPublic()->willReturn(false);
        $this->containerBuilder->findTaggedServiceIds('knp_menu.menu_builder')->willReturn(['id' => [['alias' => 'foo']]]);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Menu builder services must be public but "id" is a private service.');
        $this->pass->process($this->containerBuilder->reveal());
    }

    public function testFailsWhenAliasIsMissing()
    {
        $this->containerBuilder->findTaggedServiceIds('knp_menu.menu_builder')->willReturn(['id' => [['alias' => '']]]);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The alias is not defined in the "knp_menu.menu_builder" tag for the service "id"');
        $this->pass->process($this->containerBuilder->reveal());
    }

    public function testFailsWhenMethodIsMissing()
    {
        $this->containerBuilder->findTaggedServiceIds('knp_menu.menu_builder')->willReturn(['id' => [['alias' => 'foo']]]);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The method is not defined in the "knp_menu.menu_builder" tag for the service "id"');
        $this->pass->process($this->containerBuilder->reveal());
    }

    public function testReplaceArgument()
    {
        $this->containerBuilder->getDefinition('id1')->willReturn($this->builderDefinition);
        $this->containerBuilder->getDefinition('id2')->willReturn($this->builderDefinition);

        $taggedServiceIds = [
            'id1' => [['alias' => 'foo', 'method' => 'fooMenu'], ['alias' => 'bar', 'method' => 'bar']],
            'id2' => [['alias' => 'foo', 'method' => 'fooBar'], ['alias' => 'baz', 'method' => 'bar']],
        ];
        $this->containerBuilder->findTaggedServiceIds('knp_menu.menu_builder')->willReturn($taggedServiceIds);

        $menuBuilders = [
            'foo' => ['id2', 'fooBar'],
            'bar' => ['id1', 'bar'],
            'baz' => ['id2', 'bar'],
        ];
        $this->definition->replaceArgument(1, $menuBuilders)->shouldBeCalled();

        $this->pass->process($this->containerBuilder->reveal());
    }
}

