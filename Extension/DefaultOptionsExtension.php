<?php

namespace Knp\Bundle\MenuBundle\Extension;

use Knp\Menu\Factory\ExtensionInterface;
use Knp\Menu\ItemInterface;

/**
 * An extension to configure the default menu options.
 *
 * @author Wouter J <wouter@wouterj.nl>
 */
class DefaultOptionsExtension implements ExtensionInterface
{
    /**
     * @var array
     */
    private $options;

    public function __construct(array $options)
    {
        $this->options = $options;
    }

    /**
     * {@inheritdoc}
     */
    public function buildOptions(array $options = array())
    {
        return array_merge($this->options, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function buildItem(ItemInterface $item, array $options)
    {
    }
}
