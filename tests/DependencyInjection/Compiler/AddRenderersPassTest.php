<?php

namespace Knp\Bundle\MenuBundle\Tests\DependencyInjection\Compiler;

use Knp\Bundle\MenuBundle\DependencyInjection\Compiler\AddRenderersPass;
use Knp\Menu\Renderer\PsrProvider;
use Knp\Menu\Renderer\TwigRenderer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Argument\ServiceClosureArgument;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class AddRenderersPassTest extends TestCase
{
    public function testProcessWithoutProviderDefinition()
    {
        $containerBuilder = $this->getMockBuilder(ContainerBuilder::class)->getMock();
        $containerBuilder->expects($this->once())
            ->method('hasDefinition')
            ->willReturn(false);
        $containerBuilder->expects($this->never())
            ->method('findTaggedServiceIds');

        $renderersPass = new AddRenderersPass();

        $renderersPass->process($containerBuilder);
    }

    public function testProcessWithEmptyAlias()
    {
        $containerBuilderMock = $this->getMockBuilder(ContainerBuilder::class)->getMock();
        $containerBuilderMock->expects($this->once())
            ->method('hasDefinition')
            ->willReturn(true);
        $containerBuilderMock->expects($this->once())
            ->method('findTaggedServiceIds')
            ->with($this->equalTo('knp_menu.renderer'))
            ->willReturn(['id' => ['tag1' => ['alias' => '']]]);

        $this->expectException(\InvalidArgumentException::class);
        $renderersPass = new AddRenderersPass();
        $renderersPass->process($containerBuilderMock);
    }

    public function testProcessWithAlias()
    {
        $containerBuilder = new ContainerBuilder();
        $containerBuilder->register('knp_menu.renderer_provider', PsrProvider::class)
            ->setArguments([new Reference('service_container'), '%knp_menu.default_renderer%', []]);

        $containerBuilder->register('test_renderer', TwigRenderer::class)
            ->addTag('knp_menu.renderer', ['alias' => 'test_alias']);

        $renderersPass = new AddRenderersPass();
        $renderersPass->process($containerBuilder);

        $providerDef = $containerBuilder->getDefinition('knp_menu.renderer_provider');
        $this->assertEquals(PsrProvider::class, $providerDef->getClass());
        $this->assertEquals('%knp_menu.default_renderer%', $providerDef->getArgument(1));
        $this->assertInstanceOf(Reference::class, $providerDef->getArgument(0));

        $locatorDef = $containerBuilder->getDefinition((string) $providerDef->getArgument(0));
        $this->assertEquals(['test_alias' => new ServiceClosureArgument(new Reference('test_renderer'))], $locatorDef->getArgument(0));
    }
}
