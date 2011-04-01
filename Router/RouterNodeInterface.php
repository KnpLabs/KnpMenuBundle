<?php

namespace Knplabs\Bundle\MenuBundle\Router;

use Knplabs\Bundle\MenuBundle\NodeInterface;

/**
 * Interface implemented by a node to construct a menu from a tree.
 */
interface RouterNodeInterface extends NodeInterface
{
    /**
     * Get the route of the link
     *
     * If this method returns null, getUri() will be used.
     * If both a route and an uri are provided, the route is used.
     *
     * @return string
     */
    function getRoute();

    /**
     * Get the parameters used to generate the route
     *
     * @return array
     */
    function getRouteParameters();

    /**
     * Whether the generated uri should be absolute
     *
     * @return boolean
     */
    function isRouteAbsolute();
}
