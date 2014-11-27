<?php

namespace Knp\Bundle\MenuBundle\Expression;

use Knp\Menu\Factory\ExtensionInterface;
use Knp\Menu\ItemInterface;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class ExpressionExtension implements ExtensionInterface
{
    private $evaluator;

    public function __construct(ExpressionEvaluator $evaluator)
    {
        $this->evaluator = $evaluator;
    }

    /**
     * {@inheritdoc}
     */
    public function buildOptions(array $options)
    {
        $evaluator = $this->evaluator;

        // evaluate expressions
        array_walk_recursive(
            $options,
            function (&$value) use ($evaluator) {
                if (is_string($value)) {
                    $value = $evaluator->evaluate($value);
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
