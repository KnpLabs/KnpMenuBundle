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
        // label expression
        if (isset($options['expression'])) {
            $options['label'] = $this->evaluator->evaluate($options['expression']);
        }

        // translation param expression
        if (isset($options['extras']['translation_params_expression'])) {
            foreach ($options['extras']['translation_params_expression'] as $key => $expression) {
                $options['extras']['translation_params'][$key] = $this->evaluator->evaluate($expression);
            }
        }

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
