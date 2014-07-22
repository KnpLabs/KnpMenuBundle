<?php

namespace Knp\Bundle\MenuBundle\Tests\EventListener;

use Knp\Bundle\MenuBundle\EventListener\VoterInitializerListener;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class MenuPassTest extends \PHPUnit_Framework_TestCase
{
    public function testHandleSubRequest()
    {
        $event = $this->getMockBuilder('Symfony\Component\HttpKernel\Event\GetResponseEvent')
            ->disableOriginalConstructor()
            ->getMock();
        $event->expects($this->once())
            ->method('getRequestType')
            ->will($this->returnValue(HttpKernelInterface::SUB_REQUEST));

        $voter = $this->getMockBuilder('Knp\Menu\Matcher\Voter\RouteVoter')
            ->disableOriginalConstructor()
            ->getMock();
        $voter->expects($this->never())
            ->method('setRequest');

        $listener = new VoterInitializerListener();
        $listener->addVoter($voter);

        $listener->onKernelRequest($event);
    }

    public function testHandleMasterRequest()
    {
        $request = new Request();

        $event = $this->getMockBuilder('Symfony\Component\HttpKernel\Event\GetResponseEvent')
            ->disableOriginalConstructor()
            ->getMock();
        $event->expects($this->once())
            ->method('getRequestType')
            ->will($this->returnValue(HttpKernelInterface::MASTER_REQUEST));
        $event->expects($this->once())
            ->method('getRequest')
            ->will($this->returnValue($request));

        $voter = $this->getMockBuilder('Knp\Menu\Matcher\Voter\RouteVoter')
            ->disableOriginalConstructor()
            ->getMock();
        $voter->expects($this->once())
            ->method('setRequest')
            ->with($this->equalTo($request));

        $listener = new VoterInitializerListener();
        $listener->addVoter($voter);
        $listener->addVoter($this->getMock('Knp\Menu\Matcher\Voter\VoterInterface'));

        $listener->onKernelRequest($event);
    }
}
