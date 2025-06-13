<?php

namespace Knp\Bundle\MenuBundle\Tests\DependencyInjection\Compiler;

use Knp\Bundle\MenuBundle\DependencyInjection\Compiler\RegisterMenusPass;
use Knp\Menu\MenuItem;
use Knp\Menu\Provider\LazyProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Argument\ServiceClosureArgument;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class RegisterMenusPassTest extends TestCase
{
    private ContainerBuilder $containerBuilder;

    private RegisterMenusPass $pass;

    protected function setUp(): void
    {
        $this->containerBuilder = new ContainerBuilder();

        $this->containerBuilder->register('knp_menu.menu_provider.lazy', LazyProvider::class)
            ->setArgument(0, null);
        $this->containerBuilder->register('id', \stdClass::class)
            ->addTag('knp_menu.menu_builder', ['alias' => 'foo', 'method' => 'fooMenu'])
            ->addTag('knp_menu.menu_builder', ['alias' => 'bar', 'method' => 'bar'])
            ->setPublic(true);
        $this->containerBuilder->register('menu_id', MenuItem::class)
            ->addTag('knp_menu.menu', ['alias' => 'baz'])
            ->setPublic(true);

        $this->pass = new RegisterMenusPass();
    }

    public function testNoopWithoutProvider(): void
    {
        $this->containerBuilder->removeDefinition('knp_menu.menu_provider.lazy');

        $this->pass->process($this->containerBuilder);

        $this->expectNotToPerformAssertions(); // We just want to test that it does not break in such case.
    }

    public function testFailsWhenBuilderAliasIsMissing(): void
    {
        $this->containerBuilder->getDefinition('id')
            ->setTags(['knp_menu.menu_builder' => [['alias' => '']]]);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The alias is not defined in the "knp_menu.menu_builder" tag for the service "id"');

        $this->pass->process($this->containerBuilder);
    }

    public function testFailsWhenBuilderMethodIsMissing(): void
    {
        $this->containerBuilder->getDefinition('id')
            ->setTags(['knp_menu.menu_builder' => [['alias' => 'foo']]]);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The method is not defined in the "knp_menu.menu_builder" tag for the service "id"');

        $this->pass->process($this->containerBuilder);
    }

    public function testFailsWhenMenuAliasIsMissing(): void
    {
        $this->containerBuilder->getDefinition('menu_id')
            ->setTags(['knp_menu.menu' => [['alias' => '']]]);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The alias is not defined in the "knp_menu.menu" tag for the service "menu_id"');

        $this->pass->process($this->containerBuilder);
    }

    public function testRegisterMenuBuilderAndMenu(): void
    {
        $expectedMenuBuilders = [
            'foo' => [new ServiceClosureArgument(new Reference('id')), 'fooMenu'],
            'bar' => [new ServiceClosureArgument(new Reference('id')), 'bar'],
            'baz' => new ServiceClosureArgument(new Reference('menu_id')),
        ];

        $this->pass->process($this->containerBuilder);

        $menuBuilders = $this->containerBuilder->getDefinition('knp_menu.menu_provider.lazy')->getArgument(0);

        $this->assertEquals($expectedMenuBuilders, $menuBuilders);
    }

    public function testMenuWinsOverBuilder(): void
    {
        $this->containerBuilder->getDefinition('menu_id')
            ->setTags(['knp_menu.menu' => [['alias' => 'foo']]]);

        $expectedMenuBuilders = [
            'foo' => new ServiceClosureArgument(new Reference('menu_id')),
            'bar' => [new ServiceClosureArgument(new Reference('id')), 'bar'],
        ];

        $this->pass->process($this->containerBuilder);

        $menuBuilders = $this->containerBuilder->getDefinition('knp_menu.menu_provider.lazy')->getArgument(0);

        $this->assertEquals($expectedMenuBuilders, $menuBuilders);
    }
}
