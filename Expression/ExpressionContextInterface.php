<?php

namespace Knp\Bundle\MenuBundle\Expression;

use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\ExpressionLanguage\ParsedExpression;


/**
 * Allows to compile and evaluate expressions within a specific context.
 *
 * @author Kevin Bond <kevinbond@gmail.com>
 */
interface ExpressionContextInterface
{
    const EXPRESSION_PREFIX = '@=';

    /**
     * The variable names used.
     *
     * @return array
     */
    public static function getNames();

    /**
     * Register functions to ExpressionLanguage.
     *
     * @param ExpressionLanguage $expressionLanguage
     */
    public static function registerFunctions(ExpressionLanguage $expressionLanguage);

    /**
     * Parses an expression.
     *
     * @param Expression|string       $expression         The expression to parse
     * @param ExpressionLanguage|null $expressionLanguage Optional instance of ExpressionLanguage to use
     *
     * @return ParsedExpression A ParsedExpression instance
     */
    public static function parse($expression, ExpressionLanguage $expressionLanguage = null);

    /**
     * Compiles an expression source code.
     *
     * @param Expression|string       $expression         The expression to compile
     * @param ExpressionLanguage|null $expressionLanguage Optional instance of ExpressionLanguage to use
     *
     * @return string The compiled PHP source code
     */
    public static function compile($expression, ExpressionLanguage $expressionLanguage = null);

    /**
     * Evaluate an expression.
     *
     * @param Expression|string $expression The expression to compile
     *
     * @return string The result of the evaluation of the expression
     */
    public function evaluate($expression);
}
