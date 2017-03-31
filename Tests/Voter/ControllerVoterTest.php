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

    /**
     * @param string|null $controller
     * @param mixed|null $itemControllers
     * @param array $parsingMap
     * @param boolean|null $expected
     *
     * @dataProvider provideData
     */
    public function testParsing($controller, $itemControllers, $parsingMap, $expected)
    {
        $request = new Request();
        $request->attributes->set('_controller', $controller);
        $container = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');
        $container->expects($this->once())
            ->method('get')
            ->with($this->equalTo('request'))
            ->will($this->returnValue($request));

        $item = $this->getMock('Knp\Menu\ItemInterface');
        $item->expects($this->any())
            ->method('getExtra')
            ->with($this->equalTo('controllers'))
            ->will($this->returnValue($itemControllers));

        $controllerNameParser = $this->getMockBuilder('Symfony\Bundle\FrameworkBundle\Controller\ControllerNameParser')
            ->disableOriginalConstructor()
            ->getMock();

        $controllerNameParser->expects($this->any())
            ->method('parse')
            ->will($this->returnCallback(function ($key) use ($parsingMap) {
                return $parsingMap[$key];
            }));

        $voter = new ControllerVoter($container, $controllerNameParser);

        $this->assertSame($expected, $voter->matchItem($item));
    }

    public function provideData()
    {
        return array(
            'no request controller' => array(
                null,
                array('MyBundle:Controller:iDontThinkSo'),
                array(),
                null
            ),
            'no item controllers' => array(
                'MyNamespace\MyBundle\MyController:matchMePlease',
                null,
                array(),
                null
            ),
            'no matching string' => array(
                'MyNamespace\MyBundle\MyController:matchMePlease',
                'MyBundle:Controller:iDontThinkSo',
                array('MyBundle:Controller:iDontThinkSo' => 'MyNamespace\MyBundle\MyController:iDontThinkSo'),
                null
            ),
            'matching string' => array(
                'MyNamespace\MyBundle\MyController:action',
                'MyBundle:Controller:action',
                array('MyBundle:Controller:action' => 'MyNamespace\MyBundle\MyController:action'),
                true
            ),
            'no matching single' => array(
                'MyNamespace\MyBundle\MyController:matchMePlease',
                array('MyBundle:Controller:iDontThinkSo'),
                array('MyBundle:Controller:iDontThinkSo' => 'MyNamespace\MyBundle\MyController:iDontThinkSo'),
                null
            ),
            'matching single' => array(
                'MyNamespace\MyBundle\MyController:action',
                array('MyBundle:Controller:action'),
                array('MyBundle:Controller:action' => 'MyNamespace\MyBundle\MyController:action'),
                true
            ),
            'no matching multiple' => array(
                'MyNamespace\MyBundle\MyController:matchMePlease',
                array('MyBundle:Controller:iDontThinkSo'),
                array('MyBundle:Controller:iDontThinkSo' => 'MyNamespace\MyBundle\MyController:iDontThinkSo'),
                null
            ),
            'matching multiple' => array(
                'MyNamespace\MyBundle\MyController:action',
                array(
                    'MyBundle:Controller:action',
                    'MyBundle:Controller:otherAction'
                ),
                array(
                    'MyBundle:Controller:action' => 'MyNamespace\MyBundle\MyController:action',
                    'MyBundle:Controller:otherAction' => 'MyNamespace\MyBundle\MyController:otherAction',
                ),
                true
            ),
        );
    }
}
