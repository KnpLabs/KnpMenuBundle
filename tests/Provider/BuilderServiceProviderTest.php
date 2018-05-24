<?php

namespace Knp\Bundle\MenuBundle\Tests\Provider;

use Knp\Bundle\MenuBundle\Provider\BuilderServiceProvider;
use PHPUnit\Framework\TestCase;

class BuilderServiceProviderTest extends TestCase
{
    public function testHas()
    {
        $provider = new BuilderServiceProvider(
            $this->prophesize('Symfony\Component\DependencyInjection\ContainerInterface')->reveal(),
            ['first' => ['first', 'method'], 'second' => ['dummy', 'menu']]
        );
        $this->assertTrue($provider->has('first'));
        $this->assertTrue($provider->has('second'));
        $this->assertFalse($provider->has('third'));
    }

    public function testGetExistingMenu()
    {
        $menu = $this->prophesize('Knp\Menu\ItemInterface');
        $container = $this->prophesize('Symfony\Component\DependencyInjection\ContainerInterface');
        $builder = $this->prophesize('Knp\Bundle\MenuBundle\Tests\Provider\Builder');
        $container->get('menu_builder')->willReturn($builder);
        $builder->build(['test' => 'foo'])->willReturn($menu);

        $provider = new BuilderServiceProvider($container->reveal(), ['default' => ['menu_builder', 'build']]);
        $this->assertSame($menu->reveal(), $provider->get('default', ['test' => 'foo']));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage The menu "non-existent" is not defined.
     */
    public function testThrowsExceptionWhenGettingUndefinedMenu()
    {
        $provider = new BuilderServiceProvider($this->prophesize('Symfony\Component\DependencyInjection\ContainerInterface')->reveal());
        $provider->get('non-existent');
    }

    /**
     * @dataProvider provideInvalidMenuDefinitions
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage The menu builder definition for the menu "invalid" is invalid. It should be an array (serviceId, method)
     */
    public function testThrowsExceptionWhenGettingInvalidMenu($definition)
    {
        $provider = new BuilderServiceProvider($this->prophesize('Symfony\Component\DependencyInjection\ContainerInterface')->reveal(), ['invalid' => $definition]);
        $provider->get('invalid');
    }

    public function provideInvalidMenuDefinitions()
    {
        return [
            'string' => ['def'],
            'missing array elements' => [['id']],
            'too much array elements' => [['id', 'method', 'foo']],
        ];
    }
}

interface Builder
{
    public function build(array $options);
}
