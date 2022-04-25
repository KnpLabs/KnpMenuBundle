<?php

namespace Knp\Bundle\MenuBundle\DependencyInjection;

use Knp\Menu\ItemInterface;
use Knp\Menu\Matcher\Voter\VoterInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class KnpMenuExtension extends Extension implements PrependExtensionInterface
{
    /**
     * Handles the knp_menu configuration.
     *
     * @param array $configs The configurations being loaded
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('menu.xml');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $this->GetDefinitionsForEnabledProviders($config['providers'], $container);

        if (isset($config['twig'])) {
            $loader->load('twig.xml');
            $container->setParameter('knp_menu.renderer.twig.template', $config['twig']['template']);
        }
        if ($config['templating']) {
            $loader->load('templating.xml');
        }

        $container->setParameter('knp_menu.default_renderer', $config['default_renderer']);

        // Register autoconfiguration rules for Symfony DI 3.3+
        if (\method_exists($container, 'registerForAutoconfiguration')) {
            $container->registerForAutoconfiguration(VoterInterface::class)
                ->addTag('knp_menu.voter');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getNamespace(): string
    {
        return 'http://knplabs.com/schema/dic/menu';
    }

    /**
     * {@inheritdoc}
     */
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

    /**
     * @param $providers
     * @param ContainerBuilder $container
     */
    private function GetDefinitionsForEnabledProviders($providers, ContainerBuilder $container): void
    {
        foreach ($providers as $builder => $enabled) {
            $this->GetDefinitionForEnabledProvider($enabled, $container, $builder);
        }
    }

    /**
     * @param $enabled
     * @param ContainerBuilder $container
     * @param $builder
     */
    private function GetDefinitionForEnabledProvider($enabled, ContainerBuilder $container, $builder): void
    {
        if ($enabled) {
            $container->getDefinition(\sprintf('knp_menu.menu_provider.%s', $builder))->addTag('knp_menu.provider');
        }
    }
}
