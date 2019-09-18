<?php

namespace Knp\Bundle\MenuBundle\Tests\DependencyInjection\Compiler;

use Knp\Bundle\MenuBundle\DependencyInjection\Compiler\RegisterMenusPass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Argument\ServiceClosureArgument;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class RegisterMenusPassTest extends TestCase
{
    private $containerBuilder;
    private $definition;

    /**
     * @var RegisterMenusPass
     */
    private $pass;

    protected function setUp(): void
    {
        if (!class_exists(ServiceClosureArgument::class)) {
            $this->markTestSkipped('The RegisterMenuPass requires Symfony DI 3.3+.');
        }

        $this->containerBuilder = $this->prophesize(ContainerBuilder::class);
        $this->definition = $this->prophesize(Definition::class);
        $this->pass = new RegisterMenusPass();

        $this->containerBuilder->hasDefinition('knp_menu.menu_provider.lazy')->willReturn(true);
        $this->containerBuilder->getDefinition('knp_menu.menu_provider.lazy')->willReturn($this->definition);

        $this->containerBuilder->removeDefinition('knp_menu.menu_provider.container_aware')->shouldBeCalled();
        $this->containerBuilder->removeDefinition('knp_menu.menu_provider.builder_service')->shouldBeCalled();

        $this->containerBuilder->findTaggedServiceIds('knp_menu.menu_builder', true)->willReturn([]);
        $this->containerBuilder->findTaggedServiceIds('knp_menu.menu', true)->willReturn([]);
    }

    public function testNoopWithoutProvider()
    {
        $this->containerBuilder->hasDefinition('knp_menu.menu_provider.lazy')->willReturn(false);

        $this->containerBuilder->findTaggedServiceIds('knp_menu.menu_builder', true)->shouldNotBeCalled();
        $this->containerBuilder->findTaggedServiceIds('knp_menu.menu', true)->shouldNotBeCalled();
        $this->containerBuilder->removeDefinition('knp_menu.menu_provider.container_aware')->shouldNotBeCalled();
        $this->containerBuilder->removeDefinition('knp_menu.menu_provider.builder_service')->shouldNotBeCalled();

        $this->pass->process($this->containerBuilder->reveal());
    }

    public function testFailsWhenBuilderAliasIsMissing()
    {
        $this->containerBuilder->findTaggedServiceIds('knp_menu.menu_builder', true)->willReturn(['id' => [['alias' => '']]]);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The alias is not defined in the "knp_menu.menu_builder" tag for the service "id"');
        $this->pass->process($this->containerBuilder->reveal());
    }

    public function testFailsWhenBuilderMethodIsMissing()
    {
        $this->containerBuilder->findTaggedServiceIds('knp_menu.menu_builder', true)->willReturn(['id' => [['alias' => 'foo']]]);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The method is not defined in the "knp_menu.menu_builder" tag for the service "id"');
        $this->pass->process($this->containerBuilder->reveal());
    }

    public function testFailsWhenMenuAliasIsMissing()
    {
        $this->containerBuilder->findTaggedServiceIds('knp_menu.menu', true)->willReturn(['id' => [['alias' => '']]]);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The alias is not defined in the "knp_menu.menu" tag for the service "id"');
        $this->pass->process($this->containerBuilder->reveal());
    }

    public function testRegisterMenuBuilderAndMenu()
    {
        $this->containerBuilder->findTaggedServiceIds('knp_menu.menu_builder', true)->willReturn([
            'id1' => [['alias' => 'foo', 'method' => 'fooMenu'], ['alias' => 'bar', 'method' => 'bar']],
            'id2' => [['alias' => 'foo', 'method' => 'fooBar'], ['alias' => 'baz', 'method' => 'bar']],
        ]);
        $this->containerBuilder->findTaggedServiceIds('knp_menu.menu', true)->willReturn(['id3' => [['alias' => 'test']]]);

        $menus = [
            'foo' => [new ServiceClosureArgument(new Reference('id2')), 'fooBar'],
            'bar' => [new ServiceClosureArgument(new Reference('id1')), 'bar'],
            'baz' => [new ServiceClosureArgument(new Reference('id2')), 'bar'],
            'test' => new ServiceClosureArgument(new Reference('id3')),
        ];
        $this->definition->replaceArgument(0, $menus)->shouldBeCalled();

        $this->pass->process($this->containerBuilder->reveal());
    }

    public function testMenuWinsOverBuilder()
    {
        $this->containerBuilder->findTaggedServiceIds('knp_menu.menu_builder', true)->willReturn([
            'id1' => [['alias' => 'foo', 'method' => 'fooMenu'], ['alias' => 'bar', 'method' => 'bar']],
        ]);
        $this->containerBuilder->findTaggedServiceIds('knp_menu.menu', true)->willReturn(['id3' => [['alias' => 'foo']]]);

        $menus = [
            'foo' => new ServiceClosureArgument(new Reference('id3')),
            'bar' => [new ServiceClosureArgument(new Reference('id1')), 'bar'],
        ];
        $this->definition->replaceArgument(0, $menus)->shouldBeCalled();

        $this->pass->process($this->containerBuilder->reveal());
    }
}
