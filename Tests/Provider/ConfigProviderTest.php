<?php

namespace Knp\Bundle\MenuBundle\Tests\Provider;

use Knp\Bundle\MenuBundle\Provider\ConfigProvider;
use Knp\Menu\Loader\ArrayLoader;
use Knp\Menu\MenuFactory;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class ConfigProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testHas()
    {
        $provider = new ConfigProvider(array('foo' => array()), new ArrayLoader(new MenuFactory()));

        $this->assertTrue($provider->has('foo'));
        $this->assertFalse($provider->has('bar'));
    }

    public function testGet()
    {
        $provider = new ConfigProvider(array('foo' => array()), new ArrayLoader(new MenuFactory()));

        $this->assertInstanceOf('Knp\Menu\ItemInterface', $provider->get('foo'));
        $this->assertInstanceOf('Knp\Menu\ItemInterface', $provider->get('foo'), 'Load from cache.');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetInvalid()
    {
        $provider = new ConfigProvider(array(), new ArrayLoader(new MenuFactory()));

        $provider->get('foo');
    }
}
