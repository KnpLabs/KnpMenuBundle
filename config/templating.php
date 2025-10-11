<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Knp\Bundle\MenuBundle\Templating\Helper\MenuHelper;

return static function (ContainerConfigurator $configurator): void {
    $configurator->parameters()->set('knp_menu.templating.helper.class', MenuHelper::class);

    $configurator
        ->services()
        ->set('knp_menu.templating.helper', '%knp_menu.templating.helper.class%')
        ->tag('templating.helper', ['alias' => 'knp_menu'])
        ->args([
            service('knp_menu.helper'),
            service('knp_menu.matcher'),
            service('knp_menu.manipulator'),
        ])
    ;
};
