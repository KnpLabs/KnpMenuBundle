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
    /**
     * @var ContainerInterface
     */
    private $container;

    private $controllerNameParser;

    /**
     * @param ContainerInterface $container
     * @param ControllerNameParser $controllerNameParser A controller name parser (optional)
     */
    public function __construct(ContainerInterface $container, ControllerNameParser $controllerNameParser = null)
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

        if (!$itemController = (string) $item->getExtra('controller', '')) {
            return null;
        }

        if ($itemController === $controller) {
            return true;
        }

        if (null === $this->controllerNameParser) {
            return null;
        }

        try {
            $itemParsedController = $this->controllerNameParser->parse($itemController);
            if ($itemParsedController === $controller) {
                return true;
            }
        } catch (\InvalidArgumentException $e) {} // fail silently, as FQCN are also allowed

        return null;
    }
}
