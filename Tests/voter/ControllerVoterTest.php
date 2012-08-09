<?php

namespace Knp\Bundle\MenuBundle\Tests\Voter;

use Knp\Bundle\MenuBundle\Voter\ControllerVoter;
use Symfony\Component\HttpFoundation\Request;

class ControllerVoterTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        if (!class_exists('Symfony\Component\HttpFoundation\Request')) {
            $this->markTestSkipped('The Symfony HttpFoundation component is not available.');
        }
    }

    public function testMatchingWithoutRequest()
    {
        $container = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');
        $item = $this->getMock('Knp\Menu\ItemInterface');
        $item->expects($this->never())
            ->method('getExtra');

        $voter = new ControllerVoter($container);

        $this->assertNull($voter->matchItem($item));
    }

    /**
     * @param string $controller
     * @param string $itemController
     * @param boolean|null $expected
     *
     * @dataProvider provideFQCNData
     */
    public function testControllerFQCNMatching($controller, $itemController, $expected)
    {
        $request = new Request();
        $request->attributes->set('_controller', $controller);
        $container = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');

        $container->expects($this->any())
            ->method('get')
            ->with($this->equalTo('request'))
            ->will($this->returnValue($request));

        $voter = new ControllerVoter($container);

        $item = $this->getMock('Knp\Menu\ItemInterface');
        $item->expects($this->any())
            ->method('getExtra')
            ->with($this->equalTo('controller'))
            ->will($this->returnValue($itemController));

        $this->assertSame($expected, $voter->matchItem($item));
    }

    public function provideFQCNData()
    {
        return array(
            'no request controller' => array(null, 'MyNamespace\MyBundle\MyController:action', null),
            'no item controller' => array('MyNamespace\MyBundle\MyController:action', null, null),
            'same controllers' => array('MyNamespace\MyBundle\MyController:action', 'MyNamespace\MyBundle\MyController:action', true),
            'different controllers' => array('MyNamespace\MyBundle\MyController:match-me', 'MyNamespace\MyBundle\MyController:no', null),
        );
    }

    /**
     * @param string $controller
     * @param string $itemController
     * @param string $parsedController
     * @param boolean|null $expected
     *
     * @dataProvider provideShortNotationData
     */
    public function testControllerParsedNameMatching($controller, $itemController, $controllerFQCN, $expected)
    {
        $request = new Request();
        $request->attributes->set('_controller', $controller);
        $container = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');

        $container->expects($this->any())
            ->method('get')
            ->with($this->equalTo('request'))
            ->will($this->returnValue($request));

        $item = $this->getMock('Knp\Menu\ItemInterface');
        $item->expects($this->any())
            ->method('getExtra')
            ->with($this->equalTo('controller'))
            ->will($this->returnValue($itemController));

        $controllerNameParser = $this->getMockBuilder('Symfony\Bundle\FrameworkBundle\Controller\ControllerNameParser')
            ->disableOriginalConstructor()
            ->getMock();

        $controllerNameParser->expects($this->once())
            ->method('parse')
            ->with($this->equalTo($itemController))
            ->will($this->returnValue($controllerFQCN));

        $voter = new ControllerVoter($container, $controllerNameParser);

        $this->assertSame($expected, $voter->matchItem($item));
    }

    public function provideShortNotationData()
    {
        return array(
            'not matching' => array('MyNamespace\MyBundle\MyController:hi-man', 'MyBundle:MyController:action', 'MyNamespace\MyBundle\MyController:action', null),
            'matching' => array('MyNamespace\MyBundle\MyController:action', 'MyBundle:MyController:action', 'MyNamespace\MyBundle\MyController:action', true),
        );
    }
}
