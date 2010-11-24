<?php

namespace Bundle\MenuBundle\Twig;

class MenuNode extends \Twig_Node
{
    /**
     * @param \Twig_NodeInterface $value
     * @param \Twig_NodeInterface $depth (optional)
     * @param \Twig_NodeInterface $template (optional)
     * @param integer $lineno
     * @param string $tag (optional)
     * @return void
     */
    public function __construct(\Twig_NodeInterface $value, \Twig_NodeInterface $depth = null, \Twig_NodeInterface $template = null,  $lineno, $tag = null)
    {
        parent::__construct(array('value' => $value, 'depth' => $depth, 'template' => $template), array(), $lineno, $tag);
    }

    /**
     * @param \Twig_Compiler $compiler
     * @return void
     */
    public function compile($compiler)
    {
        $compiler->addDebugInfo($this);

        $compiler
            ->write("echo \$this->env->getExtension('menu')->get(")
            ->subcompile($this->getNode('value'))
            ->raw(")->render(");

        if (null !== $this->getNode('depth')) {
            $compiler->subcompile($this->getNode('depth'));
        }

        if (null !== $this->getNode('template')) {
            $compiler->subcompile($this->getNode('template'));
        }

        $compiler->raw(");\n");
    }
}
