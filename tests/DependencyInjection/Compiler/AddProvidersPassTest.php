<?php

namespace Knp\Bundle\MenuBundle\Tests\DependencyInjection\Compiler;

use Knp\Bundle\MenuBundle\DependencyInjection\Compiler\AddProvidersPass;
use Knp\Menu\Provider\ChainProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Argument\IteratorArgument;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class AddProvidersPassTest extends TestCase
{
    public function testProcessWithoutProviderDefinition(): void
    {
        $containerBuilder = new ContainerBuilder();
        (new AddProvidersPass())->process($containerBuilder);

        self::assertFalse($containerBuilder->hasAlias('knp_menu.menu_provider'));
    }

    public function testProcessForOneProvider(): void
    {
        $containerBuilder = new ContainerBuilder();
        $containerBuilder->register('knp_menu.menu_provider.chain', ChainProvider::class);
        $containerBuilder->register('id')->addTag('knp_menu.provider');

        (new AddProvidersPass())->process($containerBuilder);

        self::assertTrue($containerBuilder->hasAlias('knp_menu.menu_provider'));
        self::assertSame('id', (string) $containerBuilder->getAlias('knp_menu.menu_provider'));
    }

    public function testProcessForManyProviders(): void
    {
        $expectedProviders = [new Reference('id'), new Reference('id2')];

        if (\class_exists(IteratorArgument::class)) {
            $expectedProviders = new IteratorArgument($expectedProviders);
        }

        $containerBuilder = new ContainerBuilder();
        $containerBuilder->register('knp_menu.menu_provider.chain', ChainProvider::class)->setArgument(0, []);
        $containerBuilder->register('id')->addTag('knp_menu.provider');
        $containerBuilder->register('id2')->addTag('knp_menu.provider');

        (new AddProvidersPass())->process($containerBuilder);

        self::assertTrue($containerBuilder->hasAlias('knp_menu.menu_provider'));
        self::assertSame('knp_menu.menu_provider.chain', (string) $containerBuilder->getAlias('knp_menu.menu_provider'));

        self::assertEquals(
            [$expectedProviders],
            $containerBuilder->getDefinition('knp_menu.menu_provider.chain')->getArguments());
    }
}
