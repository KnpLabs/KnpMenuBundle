<?php

namespace Knp\Bundle\MenuBundle\Tests\Provider;

use Knp\Bundle\MenuBundle\Provider\BuilderServiceProvider;

class BuilderServiceProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testHas()
    {
        $provider = new BuilderServiceProvider(
            $this->prophesize('Symfony\Component\DependencyInjection\ContainerInterface')->reveal(),
            array('first' => array('first', 'method'), 'second' => array('dummy', 'menu'))
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
        $builder->build(array('test' => 'foo'))->willReturn($menu);

        $provider = new BuilderServiceProvider($container->reveal(), array('default' => array('menu_builder', 'build')));
        $this->assertSame($menu->reveal(), $provider->get('default', array('test' => 'foo')));
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
        $provider = new BuilderServiceProvider($this->prophesize('Symfony\Component\DependencyInjection\ContainerInterface')->reveal(), array('invalid' => $definition));
        $provider->get('invalid');
    }

    public function provideInvalidMenuDefinitions()
    {
        return array(
            'string' => array('def'),
            'missing array elements' => array(array('id')),
            'too much array elements' => array(array('id', 'method', 'foo')),
        );
    }
}

interface Builder
{
    public function build(array $options);
}
