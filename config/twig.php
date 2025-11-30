<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Knp\Menu\Renderer\TwigRenderer;
use Knp\Menu\Twig\MenuExtension;
use Knp\Menu\Twig\MenuRuntimeExtension;

return static function (ContainerConfigurator $configurator): void {
    $configurator->parameters()->set('knp_menu.renderer.twig.options', []);

    $services = $configurator->services();

    $services
        ->set('knp_menu.twig.extension', MenuExtension::class)
        ->tag('twig.extension')
    ;

    $services
        ->set('knp_menu.twig.runtime', MenuRuntimeExtension::class)
        ->tag('twig.runtime')
        ->args([
            service('knp_menu.helper'),
            service('knp_menu.matcher'),
            service('knp_menu.manipulator'),
        ])
    ;

    $services
        ->set('knp_menu.renderer.twig', TwigRenderer::class)
        ->tag('knp_menu.renderer', ['alias' => 'twig'])
        ->args([
            service('twig'),
            '%knp_menu.renderer.twig.template%',
            service('knp_menu.matcher'),
            '%knp_menu.renderer.twig.options%',
        ])
    ;
};
