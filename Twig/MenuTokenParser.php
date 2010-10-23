<?php

namespace Bundle\MenuBundle\Twig;

use Bundle\MenuBundle\Twig\MenuNode;

class MenuTokenParser extends \Twig_TokenParser
{
    /**
     * @param \Twig_Token  $token
     * @return \Application\MenuBundle\Node\MenuNode
     */
    public function parse(\Twig_Token $token)
    {
        $value = $this->parser->getExpressionParser()->parseExpression();

        $this->parser->getStream()->expect(\Twig_Token::BLOCK_END_TYPE);

        return new MenuNode($value, $token->getLine(), $this->getTag());
    }

    /**
     * @return string
     */
    public function getTag()
    {
        return 'menu';
    }
}
