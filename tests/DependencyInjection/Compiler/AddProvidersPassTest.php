<?php

namespace Knp\Bundle\MenuBundle\Tests\DependencyInjection\Compiler;

use Knp\Bundle\MenuBundle\DependencyInjection\Compiler\AddProvidersPass;
use Knp\Menu\Provider\ChainProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class AddProvidersPassTest extends TestCase
{
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
        $containerBuilder = new ContainerBuilder();
        $containerBuilder->register('knp_menu.menu_provider.chain', ChainProvider::class)->setArgument(0, []);
        $containerBuilder->register('id')->addTag('knp_menu.provider');
        $containerBuilder->register('id2')->addTag('knp_menu.provider');

        (new AddProvidersPass())->process($containerBuilder);

        self::assertTrue($containerBuilder->hasAlias('knp_menu.menu_provider'));
        self::assertSame('knp_menu.menu_provider.chain', (string) $containerBuilder->getAlias('knp_menu.menu_provider'));
    }
}
