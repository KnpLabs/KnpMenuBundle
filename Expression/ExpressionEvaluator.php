<?php

namespace Knp\Bundle\MenuBundle\Expression;

use Symfony\Component\ExpressionLanguage\SerializedParsedExpression;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class ExpressionEvaluator
{
    const EXPRESSION_PREFIX = '@=';

    private $expressionLanguage;
    private $requestStack;
    private $security;
    private $context;

    public function __construct(ExpressionLanguage $expressionLanguage, RequestStack $requestStack, SecurityContextInterface $security)
    {
        $this->expressionLanguage = $expressionLanguage;
        $this->requestStack = $requestStack;
        $this->security = $security;
    }

    /**
     * @return array The variables used
     */
    public static function getVariableNames()
    {
        return array('user', 'request', 'security');
    }

    /**
     * @param string $expression Normal string or serialized expression
     *
     * @return string
     */
    public function evaluate($expression)
    {
        if (0 !== strpos($expression, self::EXPRESSION_PREFIX)) {
            return $expression;
        }

        return $this->expressionLanguage->evaluate(
            new SerializedParsedExpression(null, ltrim($expression, self::EXPRESSION_PREFIX)),
            $this->getContext()
        );
    }

    /**
     * @return array
     */
    protected function getContext()
    {
        if ($this->context) {
            return $this->context;
        }

        $token = $this->security->getToken();
        $user = null === $token ? null : $token->getUser();

        return array(
            'user' => $user,
            'request' => $this->requestStack->getMasterRequest(),
            'security' => $this->security,
        );
    }
}
