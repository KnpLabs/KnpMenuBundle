<?php

namespace Knplabs\MenuBundle;

/**
 * Factory to create a menu from a tree
 */
class MenuFactory
{
    /**
     * Create a menu item from a NodeInterface
     *
     * @param NodeInterface $node
     * @return MenuItem
     */
    public function createFromNode(NodeInterface $node)
    {
        $item = new MenuItem($node->getName(), $node->getUri(), $node->getAttributes());
        $item->setLabel($node->getLabel());

        foreach ($node->getChildren() as $childNode) {
            $item->addChild($this->createFromNode($childNode));
        }

        return $item;
    }
}
