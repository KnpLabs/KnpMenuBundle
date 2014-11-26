<?php

namespace Knp\Bundle\MenuBundle\Expression;

use Symfony\Component\ExpressionLanguage\ExpressionLanguage as BaseExpressionLanguage;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class ExpressionLanguage extends BaseExpressionLanguage
{
    /**
     * {@inheritdoc}
     */
    protected function registerFunctions()
    {
        parent::registerFunctions();

        $this->register(
            'is_granted',
            function ($role) {
                return sprintf('$security->isGranted(%s)', $role);
            },
            function ($variables, $role) {
                return $variables['security']->isGranted($role);
            }
        );
    }
}
