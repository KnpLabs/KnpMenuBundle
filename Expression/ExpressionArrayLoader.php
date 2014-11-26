<?php

namespace Knp\Bundle\MenuBundle\Expression;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Knp\Menu\Loader\ArrayLoader;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class ExpressionArrayLoader extends ArrayLoader
{
    private $factory;
    private $evaluator;

    public function __construct(FactoryInterface $factory, ExpressionEvaluator $evaluator)
    {
        $this->factory = $factory;
        $this->evaluator = $evaluator;
    }

    /**
     * {@inheritdoc}
     */
    public function load($data)
    {
        if (!$this->supports($data)) {
            throw new \InvalidArgumentException(sprintf('Unsupported data. Expected an array but got ', is_object($data) ? get_class($data) : gettype($data)));
        }

        return $this->fromArray($data);
    }

    /**
     * @param array       $data
     * @param string|null $name (the name of the item, used only if there is no name in the data themselves)
     *
     * @return ItemInterface
     */
    private function fromArray(array $data, $name = null)
    {
        $name = isset($data['name']) ? $data['name'] : $name;

        if (isset($data['children'])) {
            $children = $data['children'];
            unset($data['children']);
        } else {
            $children = array();
        }

        $item = $this->factory->createItem($name, $data);

        foreach ($children as $name => $child) {
            if (isset($child['show_if']) && !$this->evaluateExpression($child['show_if'])) {
                continue;
            }

            if (isset($child['hide_if']) && $this->evaluateExpression($child['hide_if'])) {
                continue;
            }

            $item->addChild($this->fromArray($child, $name));
        }

        return $item;
    }

    /**
     * @param string $expression
     *
     * @return bool
     */
    private function evaluateExpression($expression)
    {
        return (bool) $this->evaluator->evaluate($expression);
    }
}
