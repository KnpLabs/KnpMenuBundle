<?php

namespace Knp\Bundle\MenuBundle\Expression;

use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\ExpressionLanguage\SerializedParsedExpression;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
abstract class AbstractExpressionContext implements ExpressionContextInterface
{
    private $expressionLanguage;

    public function __construct(ExpressionLanguage $expressionLanguage = null)
    {
        $this->expressionLanguage = $expressionLanguage ?: new ExpressionLanguage();

        static::registerFunctions($this->expressionLanguage);
    }

    /**
     * {@inheritdoc}
     */
    public static function parse($expression, ExpressionLanguage $expressionLanguage = null)
    {
        $expressionLanguage = $expressionLanguage ?: new ExpressionLanguage();

        return $expressionLanguage->parse($expression, static::getNames());
    }

    /**
     * {@inheritdoc}
     */
    public static function compile($expression, ExpressionLanguage $expressionLanguage = null)
    {
        $expressionLanguage = $expressionLanguage ?: new ExpressionLanguage();

        return $expressionLanguage->compile($expression, static::getNames());
    }

    /**
     * {@inheritdoc}
     */
    public function evaluate($expression)
    {
        if (0 !== strpos($expression, static::EXPRESSION_PREFIX)) {
            return $expression;
        }

        return $this->expressionLanguage->evaluate(
            new SerializedParsedExpression(null, ltrim($expression, self::EXPRESSION_PREFIX)),
            $this->getValues()
        );
    }

    /**
     * Get the values to be used within this context.
     *
     * @return array
     */
    abstract protected function getValues();
}
