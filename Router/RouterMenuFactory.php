<?php

namespace Knplabs\Bundle\MenuBundle\Router;

use Knplabs\Bundle\MenuBundle\MenuFactory;
use Knplabs\Bundle\MenuBundle\NodeInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * Factory to create a menu from a tree
 */
class RouterMenuFactory extends MenuFactory
{
    /**
     * @var Symfony\Component\Routing\RouterInterface
     */
    protected $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * {@inheritdoc}
     */
    protected function getUriFromNode(NodeInterface $node)
    {
        if ($node instanceof RouterNodeInterface && null !== $node->getRoute()) {
            return $this->router->generate($node->getRoute(), $node->getRouteParameters(), $node->isRouteAbsolute());
        }

        return $node->getUri();
    }
}
