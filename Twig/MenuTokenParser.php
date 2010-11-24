<?php

namespace Bundle\MenuBundle\Twig;

use Bundle\MenuBundle\Twig\MenuNode;

class MenuTokenParser extends \Twig_TokenParser
{
    /**
     * @param \Twig_Token  $token
     * @return \Bundle\MenuBundle\Twig\MenuNode
     * @throws \Twig_SyntaxError
     */
    public function parse(\Twig_Token $token)
    {
        $lineno = $token->getLine();
        $stream = $this->parser->getStream();

        $value = $this->parser->getExpressionParser()->parseExpression();

        $depth  = null;
        if ($stream->test('depth')) {
            $stream->next();
            $depth = $this->parser->getExpressionParser()->parseExpression();
        } elseif (!$stream->test(\Twig_Token::BLOCK_END_TYPE)) {
            throw new \Twig_SyntaxError(sprintf('Unexpected token. Twig was looking for the "depth" keyword line %s)', $lineno), -1);
        }

        $template  = null;
        if ($stream->test('template')) {
            $stream->next();
            $template = $this->parser->getExpressionParser()->parseExpression();
        } elseif (!$stream->test(\Twig_Token::BLOCK_END_TYPE)) {
            throw new \Twig_SyntaxError(sprintf('Unexpected token. Twig was looking for the "template" keyword line %s)', $lineno), -1);
        }

        $stream->expect(\Twig_Token::BLOCK_END_TYPE);

        return new MenuNode($value, $depth, $template, $lineno, $this->getTag());
    }

    /**
     * @return string
     */
    public function getTag()
    {
        return 'menu';
    }
}
