<?php


namespace Knp\Bundle\MenuBundle\Tests\Provider;


use Knp\Bundle\MenuBundle\Provider\ContainerAwareBuilderProvider;

class ContainerAwareBuilderProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ContainerAwareBuilderProvider
     */
    private $provider;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $container;

    protected function setUp()
    {
        parent::setUp();

        $this->provider = new ContainerAwareBuilderProvider(
            $this->container = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface')
        );
    }

    public function testHave()
    {
        $this->assertFalse($this->provider->has('foo'));
        $this->assertFalse($this->provider->has('foo:bar'));
        $this->assertFalse($this->provider->has('foo:bar:baz'));
        $this->assertTrue($this->provider->has('@foo.bar:baz'));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Method "invalidMethod" was not found
     */
    public function testGetNonExistentMenuMethod()
    {
        $this
            ->container
            ->expects($this->once())
            ->method('get')
            ->with('menu.builder')
            ->will($this->returnValue($service = $this->getMock('stdClass')));

        $this->provider->get('@menu.builder:invalidMethod');
    }

    /**
     * @expectedException \UnexpectedValueException
     * @expectedExceptionMessage Method "mainMenu" did not return an ItemInterface menu object
     */
    public function testGetInvalidReturnValue()
    {
        $this
            ->container
            ->expects($this->once())
            ->method('get')
            ->with('menu.builder')
            ->will($this->returnValue($service = $this->getMock('stdClass', array('mainMenu'))));

        $service
            ->expects($this->once())
            ->method('mainMenu')
            ->with($options = array('foo' => 'bar'))
            ->will($this->returnValue(new \stdClass()));

        $this->provider->get('@menu.builder:mainMenu', $options);
    }

    public function testGet()
    {
        $this
            ->container
            ->expects($this->once())
            ->method('get')
            ->with('menu.builder')
            ->will($this->returnValue($service = $this->getMock('stdClass', array('mainMenu'))));

        $service
            ->expects($this->once())
            ->method('mainMenu')
            ->with($options = array('foo' => 'bar'))
            ->will($this->returnValue($item = $this->getMock('Knp\Menu\ItemInterface')));

        $this->assertSame($item, $this->provider->get('@menu.builder:mainMenu', $options));
    }
}
