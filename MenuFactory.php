<?php

namespace Knplabs\Bundle\MenuBundle;

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
        $item = new MenuItem($node->getName(), $this->getUriFromNode($node), $node->getAttributes());
        $item->setLabel($node->getLabel());

        foreach ($node->getChildren() as $childNode) {
            $item->addChild($this->createFromNode($childNode));
        }

        return $item;
    }

    /**
     * Creates a new menu item (and tree if $data['children'] is set).
     *
     * The source is an array of data that should match the output from MenuItem->toArray().
     *
     * @param  array $data The array of data to use as a source for the menu tree
     * @return MenuItem
     */
    public function createFromArray(array $data)
    {
        $class = isset($data['class']) ? $data['class'] : 'MenuItem';

        $name = isset($data['name']) ? $data['name'] : null;
        $menu = new $class($name);
        $menu->fromArray($data);

        return $menu;
    }

    /**
     * Get the uri for the given node
     *
     * @param NodeInterface $node
     * @return string
     */
    protected function getUriFromNode(NodeInterface $node)
    {
        return $node->getUri();
    }
}
