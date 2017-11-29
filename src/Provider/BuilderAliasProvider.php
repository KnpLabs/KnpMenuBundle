<?php

namespace Knp\Bundle\MenuBundle\Provider;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Knp\Menu\Provider\MenuProviderInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * A menu provider that allows for an AcmeBundle:Builder:mainMenu shortcut syntax
 *
 * @author Ryan Weaver <ryan@knplabs.com>
 */
class BuilderAliasProvider implements MenuProviderInterface
{
    private $kernel;

    private $container;

    private $menuFactory;

    private $builders = array();

    public function __construct(KernelInterface $kernel, ContainerInterface $container, FactoryInterface $menuFactory)
    {
        $this->kernel = $kernel;
        $this->container = $container;
        $this->menuFactory = $menuFactory;
    }

    /**
     * Looks for a menu with the bundle:class:method format
     *
     * For example, AcmeBundle:Builder:mainMenu would create and instantiate
     * an Acme\DemoBundle\Menu\Builder class and call the mainMenu() method
     * on it. The method is passed the menu factory.
     *
     * @param string $name    The alias name of the menu
     * @param array  $options
     *
     * @return \Knp\Menu\ItemInterface
     * @throws \InvalidArgumentException
     */
    public function get($name, array $options = array())
    {
        if (!$this->has($name)) {
            throw new \InvalidArgumentException(sprintf('Invalid pattern passed to AliasProvider - expected "bundle:class:method", got "%s".', $name));
        }

        list($bundleName, $className, $methodName) = explode(':', $name);

        $builder = $this->getBuilder($bundleName, $className);
        if (!method_exists($builder, $methodName)) {
            throw new \InvalidArgumentException(sprintf('Method "%s" was not found on class "%s" when rendering the "%s" menu.', $methodName, $className, $name));
        }

        $menu = $builder->$methodName($this->menuFactory, $options);
        if (!$menu instanceof ItemInterface) {
            throw new \InvalidArgumentException(sprintf('Method "%s" did not return an ItemInterface menu object for menu "%s"', $methodName, $name));
        }

        return $menu;
    }

    /**
     * Verifies if the given name follows the bundle:class:method alias syntax.
     *
     * @param string $name    The alias name of the menu
     * @param array  $options
     *
     * @return Boolean
     */
    public function has($name, array $options = array())
    {
        return 2 == substr_count($name, ':');
    }

    /**
     * Creates and returns the builder that lives in the given bundle
     *
     * The convention is to look in the Menu namespace of the bundle for
     * this class, to instantiate it with no arguments, and to inject the
     * container if the class is ContainerAware.
     *
     * @param string $bundleName
     * @param string $className  The class name of the builder
     *
     * @return object
     *
     * @throws \InvalidArgumentException If the class does not exist
     */
    protected function getBuilder($bundleName, $className)
    {
        $name = sprintf('%s:%s', $bundleName, $className);

        if (!isset($this->builders[$name])) {
            $class = null;
            $logs = array();
            $bundles = array();

            $allBundles = $this->kernel->getBundle($bundleName, false);

            // In Symfony 4, bundle inheritance is gone, so there is no way to get an array anymore.
            if (!is_array($allBundles)) {
                $allBundles = array($allBundles);
            }

            foreach ($allBundles as  $bundle) {
                $try = $bundle->getNamespace().'\\Menu\\'.$className;
                if (class_exists($try)) {
                    $class = $try;
                    break;
                }

                $logs[] = sprintf('Class "%s" does not exist for menu builder "%s".', $try, $name);
                $bundles[] = $bundle->getName();
            }

            if (null === $class) {
                if (1 === count($logs)) {
                    throw new \InvalidArgumentException($logs[0]);
                }

                throw new \InvalidArgumentException(sprintf('Unable to find menu builder "%s" in bundles %s.', $name, implode(', ', $bundles)));
            }

            $builder = new $class();
            if ($builder instanceof ContainerAwareInterface) {
                $builder->setContainer($this->container);
            }

            $this->builders[$name] = $builder;
        }

        return $this->builders[$name];
    }
}
