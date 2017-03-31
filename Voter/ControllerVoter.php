<?php

namespace Knp\Bundle\MenuBundle\Voter;

use Knp\Menu\ItemInterface;
use Knp\Menu\Matcher\Voter\VoterInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\ControllerNameParser;

/**
 * Voter based on the controller
 */
class ControllerVoter implements VoterInterface
{
    private $container;

    private $controllerNameParser;

    public function __construct(ContainerInterface $container, ControllerNameParser $controllerNameParser)
    {
        $this->container = $container;
        $this->controllerNameParser = $controllerNameParser;
    }

    /**
     * {@inheritdoc}
     */
    public function matchItem(ItemInterface $item)
    {
        if (null === $request = $this->container->get('request')) {
            return null;
        }

        if (null === $controller = $request->attributes->get('_controller')) {
            return null;
        }

        if (!$itemControllers = (array) $item->getExtra('controllers', array())) {
            return null;
        }

        foreach ($itemControllers as $itemController) {
            $itemParsedController = $this->controllerNameParser->parse($itemController);
            if ($itemParsedController === $controller) {
                return true;
            }
        }

        return null;
    }
}
