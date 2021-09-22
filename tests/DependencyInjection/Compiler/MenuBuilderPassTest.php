<?php

namespace Knp\Bundle\MenuBundle\Tests\DependencyInjection\Compiler;

use Knp\Bundle\MenuBundle\DependencyInjection\Compiler\MenuBuilderPass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class MenuBuilderPassTest extends TestCase
{
    /**
     * @var ContainerBuilder
     */
    private $containerBuilder;

    /**
     * @var MenuBuilderPass
     */
    private $pass;

    protected function setUp(): void
    {
        $this->containerBuilder = new ContainerBuilder();
        $this->pass = new MenuBuilderPass();

        $this->containerBuilder->register('knp_menu.menu_provider.builder_service', \stdClass::class)
            ->setArgument(0, null)
            ->setArgument(1, null);
        $this->containerBuilder->register('id', \stdClass::class)
            ->addTag('knp_menu.menu_builder', ['alias' => 'foo', 'method' => 'fooMenu'])
            ->addTag('knp_menu.menu_builder', ['alias' => 'bar', 'method' => 'bar'])
            ->setPublic(true);
    }

    public function testNoopWithoutProvider(): void
    {
        $this->containerBuilder->removeDefinition('knp_menu.menu_provider.builder_service');
        $previousDefinitions = $this->containerBuilder->getDefinitions();

        $this->pass->process($this->containerBuilder);

        $this->assertSame($previousDefinitions, $this->containerBuilder->getDefinitions());
    }

    public function testFailsWhenServiceIsAbstract(): void
    {
        $this->containerBuilder->getDefinition('id')->setAbstract(true);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Abstract services cannot be registered as menu builders but "id" is.');

        $this->pass->process($this->containerBuilder);
    }

    public function testFailsWhenServiceIsPrivate(): void
    {
        $this->containerBuilder->getDefinition('id')->setPublic(false);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Menu builder services must be public but "id" is a private service.');

        $this->pass->process($this->containerBuilder);
    }

    public function testFailsWhenAliasIsMissing(): void
    {
        $this->containerBuilder->getDefinition('id')
            ->setTags(['knp_menu.menu_builder' => [['alias' => '']]]);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The alias is not defined in the "knp_menu.menu_builder" tag for the service "id"');

        $this->pass->process($this->containerBuilder);
    }

    public function testFailsWhenMethodIsMissing(): void
    {
        $this->containerBuilder->getDefinition('id')
            ->setTags(['knp_menu.menu_builder' => [['alias' => 'foo']]]);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The method is not defined in the "knp_menu.menu_builder" tag for the service "id"');

        $this->pass->process($this->containerBuilder);
    }

    public function testReplaceArgument(): void
    {
        $this->containerBuilder->register('id2', \stdClass::class)
            ->addTag('knp_menu.menu_builder', ['alias' => 'foo', 'method' => 'fooBar'])
            ->addTag('knp_menu.menu_builder', ['alias' => 'baz', 'method' => 'bar'])
            ->setPublic(true);

        $menuBuilders = [
            'foo' => ['id2', 'fooBar'],
            'bar' => ['id', 'bar'],
            'baz' => ['id2', 'bar'],
        ];

        $this->pass->process($this->containerBuilder);

        $this->assertSame($menuBuilders, $this->containerBuilder->getDefinition('knp_menu.menu_provider.builder_service')->getArgument(1));
    }
}
