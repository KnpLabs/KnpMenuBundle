<?php

namespace Knp\Bundle\MenuBundle\Tests\DependencyInjection;

use Knp\Bundle\MenuBundle\DependencyInjection\KnpMenuExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Parameter;
use Symfony\Component\DependencyInjection\Reference;

class FOSAdvancedEncoderExtensionTest extends \PHPUnit_Framework_TestCase
{
    public function testDefault()
    {
        $container = new ContainerBuilder();
        $loader = new KnpMenuExtension();
        $loader->load(array(array()), $container);
        $this->assertTrue($container->hasDefinition('knp_menu.renderer.list'));
        $this->assertTrue($container->hasDefinition('knp_menu.renderer.twig'));
        $this->assertEquals('knp_menu.html.twig', $container->getParameter('knp_menu.renderer.twig.template'));
        $this->assertFalse($container->hasDefinition('knp_menu.templating.helper'));
    }

    public function testEnableTwig()
    {
        $container = new ContainerBuilder();
        $loader = new KnpMenuExtension();
        $loader->load(array(array('twig' => true)), $container);
        $this->assertTrue($container->hasDefinition('knp_menu.renderer.twig'));
        $this->assertEquals('knp_menu.html.twig', $container->getParameter('knp_menu.renderer.twig.template'));
    }

    public function testOverwriteTwigTemplate()
    {
        $container = new ContainerBuilder();
        $loader = new KnpMenuExtension();
        $loader->load(array(array('twig' => array('template' => 'foobar'))), $container);
        $this->assertTrue($container->hasDefinition('knp_menu.renderer.twig'));
        $this->assertEquals('foobar', $container->getParameter('knp_menu.renderer.twig.template'));
    }

    public function testDisableTwig()
    {
        $container = new ContainerBuilder();
        $loader = new KnpMenuExtension();
        $loader->load(array(array('twig' => false)), $container);
        $this->assertTrue($container->hasDefinition('knp_menu.renderer.list'));
        $this->assertFalse($container->hasDefinition('knp_menu.renderer.twig'));
    }

    public function testEnsablePhpTemplates()
    {
        $container = new ContainerBuilder();
        $loader = new KnpMenuExtension();
        $loader->load(array(array('templating' => true)), $container);
        $this->assertTrue($container->hasDefinition('knp_menu.templating.helper'));
    }
}
