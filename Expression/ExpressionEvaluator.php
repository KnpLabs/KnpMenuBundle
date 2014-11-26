<?php

namespace Knp\Bundle\MenuBundle\Expression;

use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class ExpressionEvaluator
{
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
     * @param $expression
     *
     * @return Expression|string
     */
    public function evaluate($expression)
    {
        return $this->expressionLanguage->evaluate($expression, $this->getContext());
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
