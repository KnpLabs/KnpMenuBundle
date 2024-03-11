<?php

namespace Knp\Bundle\MenuBundle\Tests\DependencyInjection;

use Knp\Bundle\MenuBundle\DependencyInjection\KnpMenuExtension;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class KnpMenuExtensionTest extends TestCase
{
    public function testDefault(): void
    {
        $container = new ContainerBuilder();
        $loader = new KnpMenuExtension();
        $loader->load([[]], $container);
        $this->assertTrue($container->hasDefinition('knp_menu.renderer.list'), 'The list renderer is loaded');
        $this->assertTrue($container->hasDefinition('knp_menu.renderer.twig'), 'The twig renderer is loaded');
        $this->assertEquals('@KnpMenu/menu.html.twig', $container->getParameter('knp_menu.renderer.twig.template'));
        $this->assertFalse($container->hasDefinition('knp_menu.templating.helper'), 'The PHP helper is not loaded');
        $this->assertTrue($container->getDefinition('knp_menu.menu_provider.builder_alias')->hasTag('knp_menu.provider'), 'The BuilderAliasProvider is enabled');
    }

    public function testEnableTwig(): void
    {
        $container = new ContainerBuilder();
        $loader = new KnpMenuExtension();
        $loader->load([['twig' => true]], $container);
        $this->assertTrue($container->hasDefinition('knp_menu.renderer.twig'));
        $this->assertEquals('@KnpMenu/menu.html.twig', $container->getParameter('knp_menu.renderer.twig.template'));
    }

    public function testOverwriteTwigTemplate(): void
    {
        $container = new ContainerBuilder();
        $loader = new KnpMenuExtension();
        $loader->load([['twig' => ['template' => 'foobar']]], $container);
        $this->assertTrue($container->hasDefinition('knp_menu.renderer.twig'));
        $this->assertEquals('foobar', $container->getParameter('knp_menu.renderer.twig.template'));
    }

    public function testDisableTwig(): void
    {
        $container = new ContainerBuilder();
        $loader = new KnpMenuExtension();
        $loader->load([['twig' => false]], $container);
        $this->assertTrue($container->hasDefinition('knp_menu.renderer.list'));
        $this->assertFalse($container->hasDefinition('knp_menu.renderer.twig'));
    }

    #[Group('legacy')]
    public function testEnablePhpTemplates(): void
    {
        $container = new ContainerBuilder();
        $loader = new KnpMenuExtension();
        $loader->load([['templating' => true]], $container);
        $this->assertTrue($container->hasDefinition('knp_menu.templating.helper'));
    }

    public function testDisableBuilderAliasProvider(): void
    {
        $container = new ContainerBuilder();
        $loader = new KnpMenuExtension();
        $loader->load([['providers' => ['builder_alias' => false]]], $container);
        $this->assertFalse($container->getDefinition('knp_menu.menu_provider.builder_alias')->hasTag('knp_menu.provider'), 'The BuilderAliasProvider is disabled');
    }
}
