<?php

namespace Knp\Bundle\MenuBundle\EventListener;

@trigger_error(sprintf('The %s class is deprecated since 2.2 and will be removed in 3.0.', VoterInitializerListener::class), E_USER_DEPRECATED);

use Knp\Menu\Matcher\Voter\VoterInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * VoterInitializerListener sets the master request in voters needing it.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class VoterInitializerListener implements EventSubscriberInterface
{
    protected $voters = array();

    public function onKernelRequest(GetResponseEvent $event)
    {
        if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
            return;
        }

        foreach ($this->voters as $voter) {
            if (method_exists($voter, 'setRequest')) {
                $voter->setRequest($event->getRequest());
            }
        }
    }

    /**
     * Adds a voter in the matcher.
     *
     * @param VoterInterface $voter
     */
    public function addVoter(VoterInterface $voter)
    {
        $this->voters[] = $voter;
    }

    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::REQUEST => 'onKernelRequest',
        );
    }
}
