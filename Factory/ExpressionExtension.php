<?php

namespace Knp\Bundle\MenuBundle\Factory;

use Knp\Bundle\MenuBundle\Expression\ExpressionContextInterface;
use Knp\Menu\Factory\ExtensionInterface;
use Knp\Menu\ItemInterface;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class ExpressionExtension implements ExtensionInterface
{
    private $expressionContext;

    public function __construct(ExpressionContextInterface $expressionContext)
    {
        $this->expressionContext = $expressionContext;
    }

    /**
     * {@inheritdoc}
     */
    public function buildOptions(array $options)
    {
        $expressionContext = $this->expressionContext;

        // evaluate expressions
        array_walk_recursive(
            $options,
            function (&$value) use ($expressionContext) {
                if (is_string($value)) {
                    $value = $expressionContext->evaluate($value);
                }
            }
        );

        return $options;
    }

    /**
     * {@inheritdoc}
     */
    public function buildItem(ItemInterface $item, array $options)
    {
        // noop
    }
}
