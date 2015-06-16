<?php


namespace Knp\Bundle\MenuBundle\Provider;


use Knp\Menu\ItemInterface;
use Knp\Menu\Provider\MenuProviderInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ContainerAwareBuilderProvider implements MenuProviderInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Looks for a menu with the @service:method format
     *
     * For example, @acme.menu.builder:mainMenu would lookup
     * service "acme.menu.builder" from the container and call
     * the mainMenu() method on it.
     * The method is passed the options.
     *
     * @param string $name
     * @param array $options
     *
     * @return \Knp\Menu\ItemInterface
     * @throws \InvalidArgumentException if the menu does not exists
     */
    public function get($name, array $options = array())
    {
        if (!$this->has($name)) {
            throw new \InvalidArgumentException(sprintf('Invalid pattern passed to ContainerAwareBuilderAliasProvider - expected "@service:method", got "%s".', $name));
        }

        list ($service, $method) = explode(':', substr($name, 1));

        $builder = $this->container->get($service);

        if (!method_exists($builder, $method)) {
            throw new \InvalidArgumentException(sprintf('Method "%s" was not found on class "%s" when rendering the "%s" menu.', $method, get_class($builder), $name));
        }

        $menu = $builder->$method($options);
        if (!$menu instanceof ItemInterface) {
            throw new \UnexpectedValueException(sprintf('Method "%s" did not return an ItemInterface menu object for menu "%s"', $method, $name));
        }

        return $menu;
    }

    /**
     * Checks whether a menu exists in this provider
     *
     * @param string $name
     * @param array $options
     *
     * @return boolean
     */
    public function has($name, array $options = array())
    {
        return 0 === strpos($name, '@') && 1 === substr_count($name, ':');
    }
}
