<?php

namespace Knp\Bundle\MenuBundle\Tests\EventListener;

use Knp\Bundle\MenuBundle\EventListener\VoterInitializerListener;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * @group legacy
 */
class VoterInitializerListenerTest extends TestCase
{
    public function testHandleSubRequest()
    {
        $event = $this->getMockBuilder('Symfony\Component\HttpKernel\Event\GetResponseEvent')
            ->disableOriginalConstructor()
            ->getMock();
        $event->expects($this->once())
            ->method('getRequestType')
            ->willReturn(HttpKernelInterface::SUB_REQUEST);

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
            ->willReturn(HttpKernelInterface::MASTER_REQUEST);
        $event->expects($this->once())
            ->method('getRequest')
            ->willReturn($request);

        $voter = $this->getMockBuilder('Knp\Menu\Matcher\Voter\RouteVoter')
            ->disableOriginalConstructor()
            ->getMock();
        $voter->expects($this->once())
            ->method('setRequest')
            ->with($this->equalTo($request));

        $listener = new VoterInitializerListener();
        $listener->addVoter($voter);
        $listener->addVoter($this->getMockBuilder('Knp\Menu\Matcher\Voter\VoterInterface')->getMock());

        $listener->onKernelRequest($event);
    }
}
