<?php

namespace Knp\Bundle\MenuBundle\DependencyInjection;

use Knp\Menu\Factory\ExtensionInterface;
use Knp\Menu\ItemInterface;
use Knp\Menu\Matcher\Voter\VoterInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

class KnpMenuExtension extends Extension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../../config'));
        $loader->load('menu.xml');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        foreach ($config['providers'] as $builder => $enabled) {
            if ($enabled) {
                $container->getDefinition(\sprintf('knp_menu.menu_provider.%s', $builder))->addTag('knp_menu.provider');
            }
        }

        if (isset($config['twig'])) {
            $loader->load('twig.xml');
            $container->setParameter('knp_menu.renderer.twig.template', $config['twig']['template']);
        }
        if ($config['templating']) {
            trigger_deprecation('knplabs/knp-menu-bundle', '3.3', 'Using the templating component is deprecated since version 3.3, this option will be removed in version 4.');
            $loader->load('templating.xml');
        }

        $container->setParameter('knp_menu.default_renderer', $config['default_renderer']);

        $container->registerForAutoconfiguration(VoterInterface::class)
            ->addTag('knp_menu.voter');
        $container->registerForAutoconfiguration(ExtensionInterface::class)
            ->addTag('knp_menu.factory_extension');
    }

    public function getNamespace(): string
    {
        return 'http://knplabs.com/schema/dic/menu';
    }

    public function getXsdValidationBasePath(): string
    {
        return __DIR__.'/../Resources/config/schema';
    }

    public function prepend(ContainerBuilder $container): void
    {
        if (!$container->hasExtension('twig')) {
            return;
        }

        $refl = new \ReflectionClass(ItemInterface::class);
        $path = \dirname($refl->getFileName()).'/Resources/views';

        $container->prependExtensionConfig('twig', ['paths' => [$path]]);
    }
}
