<?php

namespace Bundle\MenuBundle\Twig;

class MenuNode extends \Twig_Node
{
    /**
     * @param \Twig_NodeInterface $value
     * @param integer $lineno
     * @param string $tag (optional)
     * @return void
     */
    public function __construct(\Twig_NodeInterface $value, $lineno, $tag = null)
    {
        parent::__construct(array('value' => $value), array(), $lineno, $tag);
    }

    /**
     * @param \Twig_Compiler $compiler
     * @return void
     */
    public function compile($compiler)
    {
        $compiler
            ->addDebugInfo($this)
            ->write("echo \$this->env->getExtension('menu')->render(")
            ->subcompile($this->getNode('value'))
            ->raw(");\n");
    }
}
