<?php

namespace Knp\Bundle\MenuBundle\Tests\DependencyInjection\Compiler;

use Knp\Bundle\MenuBundle\DependencyInjection\Compiler\AddVotersPass;
use Knp\Menu\Matcher\Matcher;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Argument\IteratorArgument;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class AddVotersPassTest extends TestCase
{
    public function testProcessWithoutProviderDefinition(): void
    {
        $containerBuilder = new ContainerBuilder();
        (new AddVotersPass())->process($containerBuilder);

        self::assertFalse($containerBuilder->has('knp_menu.matcher'));
    }

    public function testProcessWithAlias(): void
    {
        $containerBuilder = new ContainerBuilder();
        $containerBuilder->register('knp_menu.matcher', Matcher::class)->setArguments([[]]);
        $containerBuilder->register('id')->addTag('knp_menu.voter');
        $containerBuilder->register('bar')->addTag('knp_menu.voter', ['priority' => -5, 'request' => false]);
        $containerBuilder->register('foo')->addTag('knp_menu.voter');

        $voters = [new Reference('id'), new Reference('foo'), new Reference('bar')];

        if (\class_exists(IteratorArgument::class)) {
            $voters = new IteratorArgument($voters);
        }

        (new AddVotersPass())->process($containerBuilder);

        self::assertEquals(
            [$voters],
            $containerBuilder->getDefinition('knp_menu.matcher')->getArguments()
        );
    }

    #[Group('legacy')]
    public function testProcessRequestAware(): void
    {
        $containerBuilder = new ContainerBuilder();
        $containerBuilder->register('knp_menu.matcher', Matcher::class)->setArguments([[]]);
        $containerBuilder->register('id')->addTag('knp_menu.voter');
        $containerBuilder->register('bar')->addTag('knp_menu.voter', ['priority' => -5, 'request' => false]);
        $containerBuilder->register('foo')->addTag('knp_menu.voter', ['request' => false]);

        $voters = [new Reference('id'), new Reference('foo'), new Reference('bar')];

        if (\class_exists(IteratorArgument::class)) {
            $voters = new IteratorArgument($voters);
        }

        (new AddVotersPass())->process($containerBuilder);

        self::assertEquals(
            [$voters],
            $containerBuilder->getDefinition('knp_menu.matcher')->getArguments()
        );
    }
}
