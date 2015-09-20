<?php

namespace Knp\Bundle\MenuBundle\Expression;

use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class DefaultExpressionContext extends AbstractExpressionContext
{
    private $requestStack;
    private $security;

    public function __construct(RequestStack $requestStack, SecurityContextInterface $security, ExpressionLanguage $expressionLanguage = null)
    {
        parent::__construct($expressionLanguage);

        $this->requestStack = $requestStack;
        $this->security = $security;
    }

    /**
     * {@inheritdoc}
     */
    public static function getNames()
    {
        return array('user', 'request', 'security');
    }

    /**
     * {@inheritdoc}
     */
    public static function registerFunctions(ExpressionLanguage $expressionLanguage)
    {
        $expressionLanguage->register(
            'is_granted',
            function ($role) {
                return sprintf('$security->isGranted(%s)', $role);
            },
            function ($variables, $role) {
                return $variables['security']->isGranted($role);
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    public function buildValues()
    {
        $token = $this->security->getToken();
        $user = null === $token ? null : $token->getUser();

        return array(
            'user' => $user,
            'request' => $this->requestStack->getMasterRequest(),
            'security' => $this->security,
        );
    }
}
