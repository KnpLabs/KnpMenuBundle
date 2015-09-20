<?php

namespace Knp\Bundle\MenuBundle\Tests\Expression;

use Knp\Bundle\MenuBundle\Expression\DefaultExpressionContext;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class DefaultExpressionContextTest extends \PHPUnit_Framework_TestCase
{
    public function testEvaluate()
    {
        $security = $this->getMock('Symfony\Component\Security\Core\SecurityContextInterface');
        $security->expects($this->exactly(2))->method('isGranted')->willReturn(true);

        $requestStack = $this->getMock('Symfony\Component\HttpFoundation\RequestStack');
        $requestStack->expects($this->once())->method('getMasterRequest')->willReturn(new Request());

        $context = new DefaultExpressionContext($requestStack, $security);

        $this->assertSame('foo', $context->evaluate('foo'), 'non-expressions are skipped');

        $expression = '@='.serialize(DefaultExpressionContext::parse("is_granted('ROLE_ADMIN') ? request.getMethod() : 'foo'")->getNodes());

        $this->assertSame('GET', $context->evaluate($expression));
        $this->assertSame('GET', $context->evaluate($expression), 'values cached');
    }

    public function testParse()
    {
        $this->assertInstanceOf(
            'Symfony\Component\ExpressionLanguage\ParsedExpression',
            DefaultExpressionContext::parse("user~request~is_granted('ROLE_USER')")
        );
    }

    public function testCompile()
    {
        $this->assertSame(
            '(($user . $request) . $security->isGranted("ROLE_USER"))',
            DefaultExpressionContext::compile("user~request~is_granted('ROLE_USER')")
        );
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Symfony\Component\Security\Core\SecurityContextInterface
     */
    private function getSecurityContextMock()
    {
        return $this->getMock('Symfony\Component\Security\Core\SecurityContextInterface');
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Symfony\Component\HttpFoundation\RequestStack
     */
    private function getRequestStackMock()
    {
        return $this->getMock('Symfony\Component\HttpFoundation\RequestStack');
    }
}
