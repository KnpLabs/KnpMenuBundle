<?php

namespace Knp\Bundle\MenuBundle\Provider;

use Knp\Menu\Loader\ArrayLoader;
use Knp\Menu\Provider\MenuProviderInterface;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class ConfigProvider implements MenuProviderInterface
{
    private $loader;
    private $config;
    private $menus;

    public function __construct(array $config, ArrayLoader $loader)
    {
        $this->config = $config;
        $this->loader = $loader;
    }

    /**
     * {@inheritdoc}
     */
    public function get($name, array $options = array())
    {
        if (!$this->has($name, $options)) {
            throw new \InvalidArgumentException(sprintf('Menu "%s" does not exist.', $name));
        }

        if (isset($this->menus[$name])) {
            return $this->menus[$name];
        }

        return $this->menus[$name] = $this->loader->load($this->config[$name]);
    }

    /**
     * {@inheritdoc}
     */
    public function has($name, array $options = array())
    {
        return isset($this->config[$name]);
    }
}
