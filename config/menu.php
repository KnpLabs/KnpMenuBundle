<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Knp\Bundle\MenuBundle\Provider\BuilderAliasProvider;
use Knp\Menu\FactoryInterface;
use Knp\Menu\Integration\Symfony\RoutingExtension;
use Knp\Menu\Matcher\Matcher;
use Knp\Menu\Matcher\MatcherInterface;
use Knp\Menu\Matcher\Voter\CallbackVoter;
use Knp\Menu\Matcher\Voter\RouteVoter;
use Knp\Menu\MenuFactory;
use Knp\Menu\Provider\ChainProvider;
use Knp\Menu\Provider\LazyProvider;
use Knp\Menu\Provider\MenuProviderInterface;
use Knp\Menu\Renderer\ListRenderer;
use Knp\Menu\Renderer\PsrProvider;
use Knp\Menu\Twig\Helper;
use Knp\Menu\Util\MenuManipulator;
use Symfony\Component\DependencyInjection\Argument\ServiceLocatorArgument;
use Symfony\Component\DependencyInjection\Argument\TaggedIteratorArgument;

return static function (ContainerConfigurator $configurator): void {
    $configurator->parameters()->set('knp_menu.renderer.list.options', []);

    $services = $configurator->services();

    $services
        ->set('knp_menu.factory', MenuFactory::class)
        ->public()
    ;

    $services
        ->set('knp_menu.factory_extension.routing', RoutingExtension::class)
        ->arg(0, service('router'))
        ->tag('knp_menu.factory_extension')
    ;

    $services
        ->set('knp_menu.helper', Helper::class)
        ->args([
            service('knp_menu.renderer_provider'),
            service('knp_menu.menu_provider'),
            service('knp_menu.manipulator'),
            service('knp_menu.matcher'),
        ])
    ;

    $services
        ->set('knp_menu.matcher', Matcher::class)
        ->public()
        ->arg(0, new TaggedIteratorArgument('knp_menu.voter'))
        ->tag('kernel.reset', ['method' => 'clear'])
    ;

    $services
        ->alias(MenuProviderInterface::class, 'knp_menu.menu_provider')
    ;

    $services
        ->set('knp_menu.menu_provider.chain', ChainProvider::class)
        ->arg(0, new TaggedIteratorArgument('knp_menu.provider'))
    ;

    $services
        ->set('knp_menu.menu_provider.lazy', LazyProvider::class)
        ->arg(0, [])
        ->tag('knp_menu.provider')
    ;

    $services
        ->set('knp_menu.menu_provider.builder_alias', BuilderAliasProvider::class)
        ->args([
            service('kernel'),
            service('service_container'),
            service('knp_menu.factory'),
        ])
    ;

    $services
        ->set('knp_menu.renderer_provider', PsrProvider::class)
        ->args([
            new ServiceLocatorArgument(new TaggedIteratorArgument('knp_menu.renderer', 'alias')),
            '%knp_menu.default_renderer%',
        ]);

    $services
        ->set('knp_menu.renderer.list', ListRenderer::class)
        ->tag('knp_menu.renderer', ['alias' => 'list'])
        ->args([
            service('knp_menu.matcher'),
            '%knp_menu.renderer.list.options%',
            '%kernel.charset%',
        ]);

    $services
        ->set('knp_menu.voter.callback', CallbackVoter::class)
        ->tag('knp_menu.voter');

    $services
        ->set('knp_menu.voter.router', RouteVoter::class)
        ->arg(0, service('request_stack'))
        ->tag('knp_menu.voter');

    $services
        ->set('knp_menu.manipulator', MenuManipulator::class)
    ;

    // Autowiring aliases
    $services->alias(FactoryInterface::class, 'knp_menu.factory');
    $services->alias(MatcherInterface::class, 'knp_menu.matcher');
    $services->alias(MenuManipulator::class, 'knp_menu.manipulator');
};
